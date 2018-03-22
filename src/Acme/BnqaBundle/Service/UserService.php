<?php
namespace Acme\BnqaBundle\Service;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Acme\BnqaBundle\Entity\User;

/**
 * $[FILE_NAME] .
 *
 * @author higan96.<higan.n@gmail.com>
 *
 */
class UserService
{
  private $manageRegistry;
  private $mailService;

  /**
   * Constructor.
   *
   * @param RegistryInterface $manageRegistry
   * @param MailService $mailService
   */
  public function __construct(RegistryInterface $manageRegistry,MailService $mailService)
  {
    $this->manageRegistry = $manageRegistry;
    $this->mailService = $mailService;
  }


  /**
   * Register User.
   *
   * @param User $user
   */
  public function registerUser(User $user)
  {
    $userRepository = $this->manageRegistry->getRepository('AcmeBnqaBundle:User');

    try{
      $userRepository->createUser($user);
      $this->mailService->sendRegistrationConfirmMail($user);
    }catch (\InvalidArgumentException $e){
      $this->mailService->sendDuplicatedRegistrationMail($user);
    }
  }
}
