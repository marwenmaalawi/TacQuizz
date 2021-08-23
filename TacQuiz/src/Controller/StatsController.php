<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Entity\PersonalInformations;
use App\Entity\PIReply;
use PhpParser\Node\Scalar\String_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Quiz;
use App\Entity\Result;
use Ob\HighchartsBundle\Highcharts\Highchart;

use Dompdf\Dompdf;
use Dompdf\Options;

class StatsController extends AbstractController
{
    /**
     * @Route("/stats", name="stats")
     */
    public function index(): Response
    {
        return $this->render('stats/index.html.twig', [
            'controller_name' => 'StatsController',
        ]);
    }
    /**
     * @return mixed
     * @Route ("/StatSuccess", name="StatSuccess")
     */
    public function StatSuccess(SessionInterface $session)
    {  $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());


        $Quizs = $this->getDoctrine()->getRepository(Quiz::class)->findBy(['user'=>$user,'public'=>true]);

        $Values=[];

        foreach ($Quizs as $Q)
        {$i=0;
        $j=1;

        if(count($Q->getResults())!=null)
        {foreach ($Q->getResults() as $QR )
            {$j=$j+1;
                if($QR->getResult()>=$Q->getMinScore())
                {$i=$i+1;

                }

            }


            array_push($Values,['success'=>($i*100)/($j-1),'failure'=>100-($i*100)/($j-1),'Quiz'=>$Q->getTitle(),'cat'=>$Q->getCategory()->getTitle()]);
        }
        }
        $Lab=array('success','failure');

        return $this->render('stats/successStats.html.twig', [

            'Val'=>$Values,

            'leg'=>$Lab,
            'user'=>$user,
            'res'=>0,
        ]);
    }

    /**
     * @param SessionInterface $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route ("/StatS/{id}", name="StatS")
     */

    public function chartAction($id,SessionInterface $session)
    {   $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());

        $Quiz=$this->getDoctrine()->getRepository(Quiz::class)->find($id);
        $session->set('quiz',$Quiz);
        $Pi=$this->getDoctrine()->getRepository(PersonalInformations::class)->findBy(['quiz'=>$Quiz]);
        $PiR=$this->getDoctrine()->getRepository(PIReply::class)->findBy(['PI'=>$Pi]);
        $information=[];
        $reply=[];
        $crypted=[];

        foreach ($PiR as $pr)
        {array_push($crypted,$pr->getCryptedId());

        }

        $crypted=array_unique($crypted,SORT_REGULAR );

        foreach ($crypted as $c) {
            foreach ($PiR as $p)
            {if ($p->getCryptedId()==$c)
            {array_push($reply, $p);
            }

            }
            array_push($information, $reply);
            $reply=[];

        }


        return $this->render('stats/statsbyUser.html.twig', array(
            'user'=>$user,
            'pi'=>$Pi,
            'info' => $information,
            'quiz'=>$Quiz,
            'res'=>0,
        ));
    }

    /**
     * @param SessionInterface $session
     * @param $info
     * @Route ("/pdf/{res}" , name="pdf")
     */
    public function pdf($res,SessionInterface $session)
    {         // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        $Quiz=$session->get('quiz');
        $Pi=$this->getDoctrine()->getRepository(PersonalInformations::class)->findBy(['quiz'=>$Quiz]);

        $PiR=$this->getDoctrine()->getRepository(PIReply::class)->findBy(['PI'=>$Pi]);
        $information=[];
        $reply=[];
        $crypted=[];
        if ($res==0){
        foreach ($PiR as $pr)
        {array_push($crypted,$pr->getCryptedId());}
        }else
        { foreach ($PiR as $pr)
        {if (($pr->getResult()->getResult()<=$res) and ($pr->getResult()->getResult()>=$res-10))
            array_push($crypted,$pr->getCryptedId());

        }

        }
        $crypted=array_unique($crypted,SORT_REGULAR );

        foreach ($crypted as $c) {
            foreach ($PiR as $p)
            {if ($p->getCryptedId()==$c)
            {array_push($reply, $p);}
            }
            array_push($information, $reply);
            $reply=[];}

            // Retrieve the HTML generated in our twig file
        $html = $this->renderView('stats/mypdf.html.twig', [
            'title' => "Welcome to our PDF Test",
            'pi'=>$Pi,
            'info' => $information,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }
    /**
     * @param SessionInterface $session
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route ("/filter/{res}/{id}", name="filter")
     */

    public function filter($res,$id,SessionInterface $session)
    {
        $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());

        $Quiz=$this->getDoctrine()->getRepository(Quiz::class)->find($id);
        $session->set('quiz',$Quiz);
        $Pi=$this->getDoctrine()->getRepository(PersonalInformations::class)->findBy(['quiz'=>$Quiz]);
        $PiR=$this->getDoctrine()->getRepository(PIReply::class)->findBy(['PI'=>$Pi]);
        $information=[];
        $reply=[];
        $crypted=[];

        foreach ($PiR as $pr)
        {if (($pr->getResult()->getResult()<=$res) and ($pr->getResult()->getResult()>=$res-10))
            array_push($crypted,$pr->getCryptedId());

        }

        $crypted=array_unique($crypted,SORT_REGULAR );
        foreach ($crypted as $c) {
            foreach ($PiR as $p)
            {if ($p->getCryptedId()==$c)
            {array_push($reply, $p);
            }

            }
            array_push($information, $reply);
            $reply=[];

        }


        return $this->render('stats/statsbyUser.html.twig', array(
            'user'=>$user,
            'pi'=>$Pi,
            'info' => $information,
            'quiz'=>$Quiz,
            'res'=>$res,
        ));
    }
    /**
     * @return mixed
     * @Route ("/filterSuccess/{res}", name="filterSuccess")
     */
    public function filterSuccess($res,SessionInterface $session)
    {  $user=$session->get('user');
        if(is_null($user)){
            return $this->redirectToRoute('connection');

        }
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('user')->getId());


        $Quizs = $this->getDoctrine()->getRepository(Quiz::class)->findBy(['user'=>$user,'public'=>true]);

        $Values=[];

        foreach ($Quizs as $Q)
        {$i=0;
            $j=1;

            if(count($Q->getResults())!=null)
            {foreach ($Q->getResults() as $QR )
            {$j=$j+1;
                if($QR->getResult()>=$Q->getMinScore())
                {$i=$i+1;

                }

            }

                if ((($i*100)/($j-1)<=$res)and (($i*100)/($j-1)>=$res-10)){
                array_push($Values,['success'=>($i*100)/($j-1),'failure'=>100-($i*100)/($j-1),'Quiz'=>$Q->getTitle(),'cat'=>$Q->getCategory()->getTitle()]);
            }
            }
        }
        $Lab=array('success','failure');

        return $this->render('stats/successStats.html.twig', [

            'Val'=>$Values,

            'leg'=>$Lab,
            'user'=>$user,
        ]);
    }


}
