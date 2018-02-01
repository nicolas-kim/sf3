<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace AppBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Tiquette\Domain\Member;
use Tiquette\Domain\MemberId;

class UserAccount implements UserInterface
{
    private $member;

    public static function fromMember(Member $member): self
    {
        return new self($member);
    }

    public function getId(): MemberId
    {
        return $this->member->getId();
    }

    public function getRoles(): array
    {
        return $this->member->getRoles();
    }

    public function getPassword(): string
    {
        return $this->member->getEncodedPassword()->getEncodedPassword();
    }

    public function getSalt(): string
    {
        return $this->member->getEncodedPassword()->getSalt();
    }

    public function getUsername(): string
    {
        return (string) $this->member->getEmail();
    }

    public function eraseCredentials(): void
    {
        // nothing to do
    }

    private function __construct(Member $member)
    {
        $this->member = $member;
    }
}
