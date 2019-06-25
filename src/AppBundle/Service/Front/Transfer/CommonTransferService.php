<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 24/04/2016
 * Time: 20:49
 */

namespace AppBundle\Service\Front\Transfer;


use AppBundle\Entity\Transfer;
use Ensepar\Html2pdfBundle\Factory\Html2pdfFactory;
use Symfony\Bundle\TwigBundle\TwigEngine;

class CommonTransferService
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var TwigEngine
     */
    private $twig;

    /**
     * @var Html2pdfFactory
     */
    private $html2PdfFactory;

    private $tmpDir;

    private $fromEmail;

    private $bccVoucher;

    private $bccInvoice;

    public function __construct(\Swift_Mailer $mailer,
                                TwigEngine $twig,
                                Html2pdfFactory $factory,
                                $tmpDir,
                                $fromEmail,
                                $bccInvoice,
                                $bccVoucher
    )
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->html2PdfFactory = $factory;
        $this->tmpDir = $tmpDir;
        $this->fromEmail = $fromEmail;
        $this->bccInvoice = $bccInvoice;
        $this->bccVoucher = $bccVoucher;
    }

    public function sendVoucher(Transfer $transfer)
    {

        try {
            $mails = [];
            $mails[] = $transfer->getCreatedBy()->getEmail();
            if ($transfer->getPassenger()->getEmail() != $transfer->getCreatedBy()->getEmail()) {
                $mails[] = $transfer->getPassenger()->getEmail();
            }
            $mailBody = $this->twig->render("@App/mails/voucher_mail.html.twig");

            $message = \Swift_Message::newInstance("[Navette de Vatry] Voucher");
            $message->setBody($mailBody, 'text/html');
            $message->setTo($mails)
                ->setFrom($this->fromEmail)
                ->setBcc($this->bccVoucher);
            if ($transfer->getType() == Transfer::PARTICULAR_COMMAND) {
                $voucherHtml = $this->twig->render("@App/pdf/voucher_particular_command.html.twig", array(
                    'transfer' => $transfer
                ));
            } else {
                if (in_array('ROLE_ADMIN', $transfer->getCreatedBy()->getRoles()) || in_array('ROLE_COMMERCIAL', $transfer->getCreatedBy()->getRoles()) || in_array('ROLE_SECRETARY', $transfer->getCreatedBy()->getRoles()) || in_array('ROLE_AGENT', $transfer->getCreatedBy()->getRoles())) {
                    $voucherHtml = $this->twig->render("@App/pdf/voucherAgent_v2.html.twig", array(
                        'transfer' => $transfer
                    ));
                } elseif (in_array('ROLE_PARTNER_AGENCY', $transfer->getCreatedBy()->getRoles())) {
                    $voucherHtml = $this->twig->render("@App/pdf/voucherPartnerAgency.html.twig", array(
                        'transfer' => $transfer
                    ));
                } else {
                    $voucherHtml = $this->twig->render("@App/pdf/voucher_v2.html.twig", array(
                        'transfer' => $transfer
                    ));
                }
            }

            $filepath = $this->tmpDir . "/voucher_" . str_replace('-', '_', $transfer->getReference()) . ".pdf";

            $html2Pdf = $this->html2PdfFactory->create();
            $html2Pdf->writeHTML($voucherHtml);
            $html2Pdf->Output($filepath, 'F');

            $attachement = \Swift_Attachment::fromPath($filepath);

            $message->attach($attachement);

            return $this->mailer->send($message);

        } catch (\Exception $e) {
            return false;
        }

    }




    public function sendDriverConfirmationVoucher(Transfer $transfer)
    {

        try {
            $mails = [];
            $mails[] = $transfer->getCreatedBy()->getEmail();
            if ($transfer->getPassenger()->getEmail() != $transfer->getCreatedBy()->getEmail()) {
                $mails[] = $transfer->getPassenger()->getEmail();
            }
            $mailBody = $this->twig->render("@App/mails/driverconfirmationvoucher_mail.html.twig");

            $message = \Swift_Message::newInstance("[Navette de Vatry] Voucher");
            $message->setBody($mailBody, 'text/html');
            $message->setTo($mails)
                ->setFrom($this->fromEmail)
                ->setBcc($this->bccVoucher);
            if ($transfer->getType() == Transfer::PARTICULAR_COMMAND) {
                $voucherHtml = $this->twig->render("@App/pdf/voucher_particular_command.html.twig", array(
                    'transfer' => $transfer
                ));
            } else {
                if (in_array('ROLE_ADMIN', $transfer->getCreatedBy()->getRoles()) || in_array('ROLE_COMMERCIAL', $transfer->getCreatedBy()->getRoles()) || in_array('ROLE_SECRETARY', $transfer->getCreatedBy()->getRoles()) || in_array('ROLE_AGENT', $transfer->getCreatedBy()->getRoles())) {
                    $voucherHtml = $this->twig->render("@App/pdf/voucherAgent_v2.html.twig", array(
                        'transfer' => $transfer
                    ));
                } elseif (in_array('ROLE_PARTNER_AGENCY', $transfer->getCreatedBy()->getRoles())) {
                    $voucherHtml = $this->twig->render("@App/pdf/voucherPartnerAgency.html.twig", array(
                        'transfer' => $transfer
                    ));
                } else {
                    $voucherHtml = $this->twig->render("@App/pdf/voucher_v2.html.twig", array(
                        'transfer' => $transfer
                    ));
                }
            }

            $filepath = $this->tmpDir . "/voucher_" . str_replace('-', '_', $transfer->getReference()) . ".pdf";

            $html2Pdf = $this->html2PdfFactory->create();
            $html2Pdf->writeHTML($voucherHtml);
            $html2Pdf->Output($filepath, 'F');

            $attachement = \Swift_Attachment::fromPath($filepath);

            $message->attach($attachement);

            return $this->mailer->send($message);

        } catch (\Exception $e) {
            return false;
        }

    }



    public function sendVoucherAgency(Transfer $transfer)
    {

        try {
            $mails = [];
            $mails[] = $transfer->getCreatedBy()->getEmail();
            if ($transfer->getPassenger()->getEmail() != $transfer->getCreatedBy()->getEmail()) {
                $mails[] = $transfer->getPassenger()->getEmail();
            }
            $mailBody = $this->twig->render("@App/mails/voucher_mail.html.twig");

            $message = \Swift_Message::newInstance("[Navette de Vatry] Voucher");
            $message->setBody($mailBody, 'text/html');
            $message->setTo($mails)
                ->setFrom($this->fromEmail)
                ->setBcc($this->bccVoucher);
            if ($transfer->getType() == Transfer::PARTICULAR_COMMAND) {
                $voucherHtml = $this->twig->render("@App/pdf/voucher_particular_command.html.twig", array(
                    'transfer' => $transfer
                ));
            } else
                $voucherHtml = $this->twig->render("@App/pdf/voucherPartnerAgency.html.twig", array(
                    'transfer' => $transfer
                ));


            $filepath = $this->tmpDir . "/voucher_" . str_replace('-', '_', $transfer->getReference()) . ".pdf";

            $html2Pdf = $this->html2PdfFactory->create();
            $html2Pdf->writeHTML($voucherHtml);
            $html2Pdf->Output($filepath, 'F');

            $attachement = \Swift_Attachment::fromPath($filepath);

            $message->attach($attachement);

            return $this->mailer->send($message);

        } catch (\Exception $e) {
            return false;
        }

    }

    public function sendConfirmationMsg(Transfer $transfer)
    {
        $mailBody = $this->twig->render("@App/mails/confirmation_msg_no_voucher.html.twig");
        $mails = [];
        $mails[] = $transfer->getCreatedBy()->getEmail();
        if ($transfer->getPassenger()->getEmail() != $transfer->getCreatedBy()->getEmail()) {
            $mails[] = $transfer->getPassenger()->getEmail();
        }
        $message = \Swift_Message::newInstance("[Navette de Vatry] Voucher");
        $message->setBody($mailBody, 'text/html');
        $message->setTo($mails)
            ->setFrom($this->fromEmail)
            ->setBcc($this->bccVoucher);
        return $this->mailer->send($message);
    }

    public function sendInvoice(Transfer $transfer)
    {
        try {
            $mailBody = $this->twig->render("@App/mails/facture_mail.html.twig");
            $message = \Swift_Message::newInstance("[Navette de Vatry] Facture");
            $message->setBody($mailBody, 'text/html');
            $message->setTo($transfer->getCreatedBy()->getEmail())
                ->setFrom($this->fromEmail)
                ->setBcc($this->bccInvoice);

            $factureHtml = $this->twig->render("@App/pdf/facture.html.twig", array(
                'transfer' => $transfer
            ));

            if (in_array('ROLE_PARTNER_AGENCY', $transfer->getCreatedBy()->getRoles())) {
                $factureHtml = $this->twig->render("@App/pdf/facturePartnerAgency.html.twig", array(
                    'transfer' => $transfer
                ));
            }

            $filepath = $this->tmpDir . "/facture_" . str_replace('-', '_', $transfer->getReference()) . ".pdf";

            $html2Pdf = $this->html2PdfFactory->create('L');
            $html2Pdf->writeHTML($factureHtml);
            $html2Pdf->Output($filepath, 'F');

            $attachement = \Swift_Attachment::fromPath($filepath);

            $message->attach($attachement);

            return $this->mailer->send($message);

        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendInvoiceAgency(Transfer $transfer)
    {
        try {
            $mailBody = $this->twig->render("@App/mails/facture_mail.html.twig");
            $message = \Swift_Message::newInstance("[Navette de Vatry] Facture");
            $message->setBody($mailBody, 'text/html');
            $message->setTo($transfer->getCreatedBy()->getEmail())
                ->setCc($transfer->getAffectedTo()->getEmail())
                ->setFrom($this->fromEmail)
                ->setBcc($this->bccInvoice);

            $factureHtml = $this->twig->render("@App/pdf/facturePartnerAgency.html.twig", array(
                'transfer' => $transfer
            ));


            $filepath = $this->tmpDir . "/facture_" . str_replace('-', '_', $transfer->getReference()) . ".pdf";

            $html2Pdf = $this->html2PdfFactory->create('L');
            $html2Pdf->writeHTML($factureHtml);
            $html2Pdf->Output($filepath, 'F');

            $attachement = \Swift_Attachment::fromPath($filepath);

            $message->attach($attachement);

            return $this->mailer->send($message);

        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendCancelEmail(Transfer $transfer)
    {
        try {
            if ($transfer->getType() == Transfer::PARTICULAR_COMMAND) {
                $mailBody = $this->twig->render("@App/mails/cancel_particular_command_mail.html.twig", array('transfert' => $transfer));
            } else {
                $mailBody = $this->twig->render("@App/mails/cancel_mail.html.twig", array('transfert' => $transfer));
            }

            $message = \Swift_Message::newInstance("[Navette de Vatry | Annulation transfert]");
            $message->setBody($mailBody, 'text/html');
            $message->setTo($transfer->getPassenger()->getEmail())
                ->setFrom($this->fromEmail)
                ->setBcc($this->bccInvoice);

            return $this->mailer->send($message);

        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendEmailConfirmB2B(Transfer $transfer)
    {
        try {
            $mailBody = $this->twig->render("@App/mails/mail_confirm_b2b.html.twig", array('transfer' => $transfer));
            $message = \Swift_Message::newInstance("[Navette de Vatry | Confirmation transfert]");
            $message->setBody($mailBody, 'text/html');
            $message->setTo($transfer->getAffectedTo()->getEmail())
                ->setFrom($this->fromEmail)
                ->setBcc($this->bccInvoice);

            return $this->mailer->send($message);

        } catch (\Exception $e) {
            return false;
        }
    }



    public function sendEmailConfirmRC(Transfer $transfer)
    {
        try {
            $mailBody = $this->twig->render("@App/mails/mail_confirm_rc.html.twig", array('transfer' => $transfer));
            $message = \Swift_Message::newInstance("[Navette de Vatry | Confirmation transfert]");
            $message->setBody($mailBody, 'text/html');
            $message->setTo($transfer->getAffectedTo()->getEmail())
                ->setFrom($this->fromEmail)
                ->setBcc($this->bccInvoice);

            return $this->mailer->send($message);

        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendCancelB2BEmail(Transfer $transfer)
    {
        try {
            $mailBody = $this->twig->render("@App/mails/mail_cancel_b2b.html.twig", array('transfer' => $transfer));
            $message = \Swift_Message::newInstance("[Navette de Vatry | VAlidation transfert]");
            $message->setBody($mailBody, 'text/html');
            $message->setTo($transfer->getCreatedBy()->getEmail())
                ->setFrom($this->fromEmail)
                ->setBcc($this->bccInvoice);

            return $this->mailer->send($message);

        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendEmailValidB2B(Transfer $transfer)
    {
        try {
            $mailBody = $this->twig->render("@App/mails/mail_valid_b2b.html.twig", array('transfer' => $transfer));
            $message = \Swift_Message::newInstance("[Navette de Vatry | Validation transfert]");
            $message->setBody($mailBody, 'text/html');
            $message->setTo($transfer->getCreatedBy()->getEmail())
                ->setFrom($this->fromEmail)
                ->setBcc($this->bccInvoice);

            return $this->mailer->send($message);

        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendUpdateDateEmail(Transfer $transfer)
    {

        try {
            $mails = [];
            $mails[] = $transfer->getCreatedBy()->getEmail();
            if ($transfer->getPassenger()->getEmail() != $transfer->getCreatedBy()->getEmail()) {
                $mails[] = $transfer->getPassenger()->getEmail();
            }
            $mailBody = $this->twig->render("@App/mails/update_date_mail.html.twig", array('transfert' => $transfer));
            $message = \Swift_Message::newInstance("[Navette de Vatry | Modification date transfert] Voucher");
            $message->setBody($mailBody, 'text/html');
            $message->setTo($mails)
                ->setFrom($this->fromEmail)
                ->setBcc($this->bccVoucher);

            if (in_array('ROLE_ADMIN', $transfer->getCreatedBy()->getRoles()) || in_array('ROLE_COMMERCIAL', $transfer->getCreatedBy()->getRoles()) || in_array('ROLE_SECRETARY', $transfer->getCreatedBy()->getRoles()) || in_array('ROLE_AGENT', $transfer->getCreatedBy()->getRoles())) {
                $voucherHtml = $this->twig->render("@App/pdf/voucherAgent_v2.html.twig", array(
                    'transfer' => $transfer
                ));
            } else {
                $voucherHtml = $this->twig->render("@App/pdf/voucher_v2.html.twig", array(
                    'transfer' => $transfer
                ));
            }
            $filepath = $this->tmpDir . "/voucher_" . str_replace('-', '_', $transfer->getReference()) . ".pdf";

            $html2Pdf = $this->html2PdfFactory->create();
            $html2Pdf->writeHTML($voucherHtml);
            $html2Pdf->Output($filepath, 'F');

            $attachement = \Swift_Attachment::fromPath($filepath);

            $message->attach($attachement);

            return $this->mailer->send($message);

        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendConfirmationForLaterPayment(Transfer $transfer)
    {
        try {
            $mailBody = $this->twig->render("@App/mails/pending_confirmation_payment.html.twig", array('transfer' => $transfer));
            $message = \Swift_Message::newInstance("[Navette de Vatry] Paiement en attente");
            $message->setBody($mailBody, 'text/html');
            $message->setTo($transfer->getCreatedBy()->getEmail())
//            $message->setTo($transfer->getAffectedTo()->getEmail())
                ->setFrom($this->fromEmail)
                ->setBcc($this->bccInvoice);

            return $this->mailer->send($message);

        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendContactEmail($data)
    {
        try {
            $mailBody = $this->twig->render("@App/mails/contact.html.twig", array('data' => $data));
            $message = \Swift_Message::newInstance("[Contact|Navette de Vatry]");
            $message->setBody($mailBody, 'text/html');
            $message->setTo("info@navettevatry.fr")
                ->setFrom($data['email']);
            $message->setReplyTo($data['email']);

            return $this->mailer->send($message);

        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendParticularCommandRequestEmail($data)
    {
        try {
            $mailBody = $this->twig->render("@App/mails/particular_command.html.twig", $data);
            $message = \Swift_Message::newInstance("[Contact|Navette de Vatry]");
            $message->setBody($mailBody, 'text/html');
            $message->setSubject('Demande de devis');
            $message->setTo("info@navettevatry.fr")
                ->setFrom($data['email']);
            $message->setReplyTo($data['email']);

            return $this->mailer->send($message);

        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendParticularCommandResponseEmail($transfer, $email, $client_name)
    {
        $data = [
            'client_name' => $client_name,
            'transfer' => $transfer
        ];

        try {
            $mailBody = $this->twig->render("@App/mails/particular_command_response.html.twig", $data);
            $message = \Swift_Message::newInstance("[Contact|Navette de Vatry]");
            $message->setBody($mailBody, 'text/html');
            $message->setSubject('Confirmation de devis');
            $message->setFrom("info@navettevatry.fr");
            $message->addCc("factures@navettevatry.fr");
            $message->setTo($email);

            return $this->mailer->send($message);

        } catch (\Exception $e) {
            return false;
        }
    }

    public function sendParticularCommandRequestEmailForPartner($data, $user)
    {
        $data['user'] = $user;
        try {
            $mailBody = $this->twig->render("@App/mails/particular_command_partner.html.twig", $data);
            $message = \Swift_Message::newInstance("[Contact|Navette de Vatry]");
            $message->setBody($mailBody, 'text/html');
            $message->setSubject('Demande de devis');
            $message->setTo("info@navettevatry.fr")
                ->setFrom($user->getEmail());
            $message->setReplyTo($user->getEmail());
            return $this->mailer->send($message);

        } catch (\Exception $e) {
            return false;
        }
    }

}
