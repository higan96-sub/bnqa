<?php

namespace Acme\BnqaBundle\Service;

use Acme\BnqaBundle\Entity\User;

/**
 * MailService.php .
 *
 * @author higan96.<higan.n@gmail.com>
 *
 */
class MailService
{
  /**
   * @var \Swift_Mailer
   */
  private $mailer;

  /**
   * @var \Twig_Environment
   */
  private $twig;

  /**
   * Constructor.
   *
   * @param \Swift_Mailer $mailer
   * @param \Twig_Environment $twig
   */
  public function __construct(\Swift_Mailer $mailer,\Twig_Environment $twig)
  {
    $this->mailer = $mailer;
    $this->twig = $twig;
  }

  /**
   * Render twig template
   *
   * @param string $template
   * @param array $variables
   *
   * @return string
   */
  protected function render($template,array $variables = array())
  {
    return $this->twig->loadTemplate($template)->render($variables);
  }

  /**
   * Send register confirm mail.
   * @param User $user
   */
  public function sendRegistrationConfirmMail(User $user)
  {
    $body = $this->render('AcmeBnqaBundle:Mail:registrationConfirm.txt',array('user',$user));

    $message = \Swift_Message::newInstance()
      ->setSubject('仮登録が完了しました')
      ->setFrom(array('noreply@bnqa.jp' => 'Bnqa運営'))
      ->setTo($user->getEmail())
      ->setBody($body);

    $this->mailer->send($message);
  }

  /**
   * Send duplicated registration mail.
   *
   * @param User $user
   */
  public function sendDuplicatedRegistrationMail(User $user)
  {
    $body = $this->render('AcmeBnqaBundle:Mail:duplicatedRegistration.txt.twig',array('user' => $user));

    $message = \Swift_Message::newInstance()
      ->setSubject('既に登録されたメールアドレスです')
      ->setFrom(array('noreply@bnqa.jp' => 'Bnqa運営'))
      ->setTo($user->getEmail())
      ->setBody($body);

    $this->mailer->send($message);
  }
}
