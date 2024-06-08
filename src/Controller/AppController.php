<?php

namespace App\Controller;

use App\Entity\Election;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Doctrine\ORM\EntityManagerInterface;
use Pontedilana\PhpWeasyPrint\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;
use Throwable;

#[Route('/', name: 'app_')]
class AppController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $elections = $entityManager->getRepository(Election::class)->findBy(['isActive' => true]);

        return $this->render('app/index.html.twig', [
            'elections' => $elections,
        ]);
    }

    #[Route('/export/{id}/html', name: 'export_qr_html', methods: ['GET'])]
    public function viewElectionCodes(Election $election, Pdf $pdf): Response
    {
        return $this->render('election/export_codes.html.twig',[
            'election' => $election
        ]);
    }

    #[Route('/swqr/{id}/{code?}', name: 'create_sw_qr', methods: ['GET'])]
    public function createSWQR(Uuid $id, ?string $code)
    {
        $data = $this->generateUrl('election_show',['uuid' => $id,'code' => $code],UrlGeneratorInterface::ABSOLUTE_URL);
        $options = new QROptions;

        $options->version              = 7;
        $options->outputInterface      = QRMarkupSVG::class;
        $options->outputBase64         = false;
        // if set to false, the light modules won't be rendered
        $options->drawLightModules     = true;
        $options->svgUseFillAttributes = false;
        // draw the modules as circles instead of squares
        $options->drawCircularModules  = false;
        $options->circleRadius         = 0.4;
        // connect paths
        $options->connectPaths         = true;
        // keep modules of these types as square
        $options->keepAsSquare         = [
            QRMatrix::M_FINDER_DARK,
            QRMatrix::M_FINDER_DOT,
            QRMatrix::M_ALIGNMENT_DARK,
        ];
        // https://developer.mozilla.org/en-US/docs/Web/SVG/Element/linearGradient
        $options->svgDefs = '<linearGradient id="rainbow" x1="1" y2="1">
            <stop stop-color="#e2453c" offset="0"/>
            <stop stop-color="#e07e39" offset="0.2"/>
            <stop stop-color="#e5d667" offset="0.4"/>
            <stop stop-color="#51b95b" offset="0.6"/>
            <stop stop-color="#1e72b7" offset="0.8"/>
            <stop stop-color="#6f5ba7" offset="1"/>
        </linearGradient>
        <style><![CDATA[
            .dark{fill: #000000;}
            .light{fill: none;}
        ]]></style>';


        try{
            $out = (new QRCode($options))->render($data);
        }
        catch(Throwable $e){
            // handle the exception in whatever way you need
            exit($e->getMessage());
        }
        if(php_sapi_name() !== 'cli'){
            header('Content-type: image/svg+xml');

            if(extension_loaded('zlib')){
                header('Vary: Accept-Encoding');
                header('Content-Encoding: gzip');
                $out = gzencode($out, 9);
            }
        }
        echo $out;
        exit;
    }

    #[Route('/privacy', name: 'privacy')]
    public function showPrivacy(): Response
    {
        return $this->render('app/privacy.html.twig');
    }

    #[Route('/imprint', name: 'imprint')]
    public function showImprint(): Response
    {
        return $this->render('app/imprint.html.twig');
    }

}
