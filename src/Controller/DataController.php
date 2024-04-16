<?php

/*
 * This file is for the APP.
 *
 * (c) ATTIOGBE DAMESSI D.<com.app50@gmail.com>
 *
 *  Load  sms from Cloud, Process, and feed the localDatabase
 */

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\SMS;

use App\Utils\SMSProcessor;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpClient\HttpClient;

use Symfony\Component\Routing\Annotation\Route;


/**
 * Controller used to manage blog contents in the public part of the site.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class DataController extends AbstractController
{

    #[Route('/feed', name: 'app_feed_new_sms')]
    public function index(Request $request,  EntityManagerInterface $em): Response
    {
         $transSMSXML ='';
         $extraSMSXML='';
         $new_trans_count = 0;
         $oldLastCloudSMSId = $this->getCloudOldSMSLastIndex(); //from local file
       
        $transSMSXML  =  $this->loadRecentsSMSFromCloud($oldLastCloudSMSId);
        if( $transSMSXML != null ){
            $smsProc     = new SMSProcessor();
            $dataAR = $smsProc->processCloudSMS($transSMSXML);
            $newLastCloudSMSId = $dataAR['DLastId'];
            $this->saveNewLastCloudSMSIndex($newLastCloudSMSId);
             if ($oldLastCloudSMSId < $newLastCloudSMSId) {
                  $new_trans = $dataAR['DTrans'];
                  $new_trans_count = sizeof($new_trans);
                  $extraSMSXML   = $dataAR['DExtraSMS'];
                  $this->feedDatabase($new_trans,$em);
            //    $this->saveExtraSMS($dataAR['DExtraSMS']);
      }
    }
           if ($request->isXmlHttpRequest()) {    
                  return new JsonResponse($new_trans_count); 
           }else{            
            return $this->render('data/index.html.twig',
               ['normalTrans' =>$transSMSXML,
                'newTransCount' =>$new_trans_count,
                'extraTrans' =>$extraSMSXML
             ]);
     }
}

#[Route('/load-sms-from/{lid}', name: 'app_feed_new_sms_from')]

 public function downloadSMSFrom(Request $request,  EntityManagerInterface $em ,int $lid): Response
 {

       $newLastCloudSMSId = $this->loadNewLastSMSIdFromCloud();
       $smsXml  =  $this->loadRecentsSMSFromCloud($lid);
       $new_sms_count = 0;
     if ($lid != $newLastCloudSMSId) { 

 }    if ($request->isXmlHttpRequest()) {  
               return new JsonResponse($new_sms_count); 
 }
 else {            
     return $this->render('data/index.html.twig', ['body' =>$smsXml, 'count' =>$new_sms_count]);
}
}

private function feedDatabase(array $dataT, EntityManagerInterface $em){
       
        $i = 0;
        for (  $i=0; $i < sizeof($dataT); $i++ ) {
                    $trans = new Transaction();
                    $trans->setBankName($dataT[$i]['Bank'])
                    ->setAmount($dataT[$i]['Amt']);
                    $trans->setPrintAmt(''); // after remove it
                    $trans->setDescription($dataT[$i]['Desc'])
                    ->setDate($dataT[$i]['Date']);
                    $em->persist($trans);
                    $em->flush();
         }
}
// save the LastIndex of the in cloud loaded message of the database
//Must use the given date from sms processor
private function saveNewLastCloudSMSIndex(int $lastCloudSMSIndex){

                file_put_contents('../data/cloudLastSMSIndex.txt',$lastCloudSMSIndex);
                $oldIndexLog = file_get_contents('../data/indexLog.txt');
                $dtn = (new \DateTime("now") )->format("d.m.y H:i:s");
                file_put_contents('../data/indexLog.txt', $oldIndexLog. chr(13).$dtn.', lastid:'.$lastCloudSMSIndex);
}


private function getCloudOldSMSLastIndex():int{
       
       $oldIndex = file_get_contents('../data/cloudLastSMSIndex.txt');
       return intval( $oldIndex );
}
private function saveExtraSMS($sms){
    $oldContent = file_get_contents('../data/extraSMS.txt');
    file_put_contents('../data/extraSMS.txt',$oldContent.chr(13).$sms);
}

private function saveCloudLastInbox(string $newSMSinboxXML){
      
      $oldInbox = file_get_contents('../data/inbox.xml');
      file_put_contents('../data/inbox.xml',$oldInbox.$newSMSinboxXML);
  }
  
// load the recents sms from cloud with the given last index
private function loadRecentsSMSFromCloud(int $lastIndex){
   
    $URL ='https://stuccoitalianotogo.com/sms-api/?lid='.$lastIndex;
    $client = HttpClient::create();
    try {
     $response = $client->request('GET',$URL);

    } catch (\Exception $e) {

        echo $e->getMessage();
    }
     $statusCode = $response->getStatusCode();
     $contentType = $response->getHeaders()['content-type'][0];
     $content = $response->getContent();
     return $content;
}

private function loadAllSMSFromCloud(){

        $client = HttpClient::create();
        $response = $client->request(
        'GET',
        'https://stuccoitalianotogo.com/sms-api/?q=all'
  );
     $statusCode = $response->getStatusCode();
     $contentType = $response->getHeaders()['content-type'][0];
     $content = $response->getContent();
     return $content;
}

private function syncLocalSMSDatabase(string $smsBody){
        
        $SMS = new SMS();
        $SMS->setBody($smsBody);
        $entityManager = $this->getDoctrine()->getRepository(SMS::class);
        $entityManager->persist($SMS);
        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();
}
}
 
