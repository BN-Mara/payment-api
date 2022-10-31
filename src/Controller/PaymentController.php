<?php

namespace App\Controller;

use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PaymentController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        
    }
    #[Route('/sapi/payment', name: 'app_payment', methods:'GET')]
    public function index(): Response
    {
        
        $payments = $this->entityManager->getRepository(Payment::class)->findAll();
        $data = [];
        foreach($payments as $pay){
            $data[] = [
                "id"=>$pay->getId(),
                "reference"=>$pay->getReference(),
                "amount"=>$pay->getAmount(),
                "email"=>$pay->getEmail(),
                "phone"=>$pay->getPhone(),
                "status"=>$pay->getStatus(),
                "currency"=>$pay->getCurrency(),
                "createdAt"=>$pay->getCreatedAt(),
                "paymentUrl"=> $this->paymentUrl($pay)
            ];
        }
        return $this->json($data);
    }
    #[Route('/sapi/payment/{id}', name: 'app_payment_one', methods:'GET')]
    public function one(int $id): Response
    {
        /*return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);*/
        $pay = $this->entityManager->getRepository(Payment::class)->find($id);
        if (!$pay) {
            return $this->json('No Payment found for id' . $id, 404);
        }
        $data = [
            "id"=>$pay->getId(),
            "reference"=>$pay->getReference(),
            "amount"=>$pay->getAmount(),
            "email"=>$pay->getEmail(),
            "phone"=>$pay->getPhone(),
            "status"=>$pay->getStatus(),
            "currency"=>$pay->getCurrency(),
            "createdAt"=>$pay->getCreatedAt(),
            "paymentUrl"=> $this->paymentUrl($pay)
        ];

        return $this->json($data);
    }
    #[Route('/sapi/payment',name: 'app_payment_create', methods:'POST')]
    
    public function create(Request $request):Response
    {
        if($request->getMethod() == 'POST')
        {
            $donnees = json_decode($request->getContent());
            $pay = new Payment();
            $ref = uniqid("XREF");
            $pay->setAmount($donnees->amount);
            $pay->setReference($ref);
            $pay->setEmail($donnees->email);
            $pay->setPhone($donnees->phone);
            $pay->setPaymentMode("MOBILE");
            $pay->setCurrency($donnees->currency);
            
            $this->entityManager->persist($pay);
            $this->entityManager->flush();
            

            $data = [
                "id"=>$pay->getId(),
                "reference"=>$pay->getReference(),
                "amount"=>$pay->getAmount(),
                "email"=>$pay->getEmail(),
                "phone"=>$pay->getPhone(),
                "status"=>$pay->getStatus(),
                "payUrl"=>"https://maxicash.com/payentry",
                "currency"=>$pay->getCurrency(),
                "createdAt"=>$pay->getCreatedAt(),
                "paymentUrl"=> $this->paymentUrl($pay)
            ];

            
    
            return $this->json($data);


        }else{
            return new JsonResponse("not allowed",501);
        }


    }

    #[Route("/sapi/payment/{id}", name: "api_payment_edit", methods:'PUT')]
    public function edit(Request $request, int $id):Response
    {
        $pay = $this->entityManager->getRepository(Payment::class)->find($id);
        if (!$pay) {
            return $this->json('No Payment found for id' . $id, 404);
        }
        $donnees = json_decode($request->getContent());
            
            $ref = uniqid("XREF");
            $pay->setAmount($donnees->amount);
            $pay->setReference($ref);
            $pay->setEmail($donnees->email);
            $pay->setPhone($donnees->phone);
            $pay->setPaymentMode($donnees->mode);
            $pay->setCurrency($donnees->currency);
            $pay->setModifiedAt(new \DateTime('now'));
            
            //$this->entityManager->persist($pay);
            $this->entityManager->flush();
            

            $data = [
                "id"=>$pay->getId(),
                "reference"=>$pay->getReference(),
                "amount"=>$pay->getAmount(),
                "email"=>$pay->getEmail(),
                "phone"=>$pay->getPhone(),
                "status"=>$pay->getStatus(),
                "currency"=>$pay->getCurrency(),
                "createdAt"=>$pay->getCreatedAt(),
                "paymentUrl"=> $this->paymentUrl($pay),
                "paymentMode"=>$pay->getPaymentMode()
            ];
   
            return $this->json($data);

    }

    #[Route("/sapi/payment/validate/{id}", name:"api_payment_validate",methods:'PUT')]
    public function validate(int $id):Response
    {
        $pay = $this->entityManager->getRepository(Payment::class)->find($id);
        if (!$pay) {
            return $this->json('No Payment found for id' . $id, 404);
        }
        $pay->setStatus(1);
        $this->entityManager->flush();
        $data = [
            "message"=>"payment accepted ref ".$pay->getReference(),
            "success"=>true
        ];
        return $this->json($data);
    }

    public function paymentUrl(Payment $payment): string{
        
        $postData = ["PayType"=>"MaxiCash","Amount"=>$payment->getAmount(),"Currency"=>$payment->getCurrency(),
        "Telephone"=>$payment->getPhone(),"Email"=>$payment->getEmail(),"MerchantID"=>"2bd7fd5caedc48dd8c5bcabee629812b","MerchantPassword"=>"55a6046137584680abddafe262985ff2",
        "Language"=>"fr","Reference"=>$payment->getReference(),"Accepturl"=>"https://maajaburafiki.com/web/success",
        "Cancelurl"=>"https://maajaburafiki.com/web/failed","Declineurl"=>"https://maajaburafiki.com/web/failed",
        "NotifyURL"=>"https://maajaburafiki.com/web/failed"];
        
        $jsonData = json_encode($postData);
        $maxiUrl = 'https://api.maxicashapp.com/payentry?data='.$jsonData;
        return $maxiUrl;
       
    }
}
