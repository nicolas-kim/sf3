<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Tiquette\Infrastructure\Repositories;

use Doctrine\DBAL\Connection;
use Tiquette\Domain\Email;
use Tiquette\Domain\Member;
use Tiquette\Domain\MemberNotFound;
use Tiquette\Domain\MemberRepository;

class DbalMemberRepository implements MemberRepository
{
    private $connection;

    public  function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function save(Member $member): void
    {
        $query = <<<SQL
INSERT INTO members
    (uuid, email, nickname, encoded_password, password_salt, roles)
VALUES
    (:uuid, :email, :nickname, :encoded_password, :password_salt, :roles)
  ON DUPLICATE KEY
    UPDATE
      encoded_password = :encoded_password,
      password_salt    = :password_salt,
      roles            = :roles
;
SQL;

        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'uuid' => (string) $member->getId(),
            'email' => (string) $member->getEmail(),
            'nickname' => $member->getNickname(),
            'encoded_password' => $member->getEncodedPassword()->getEncodedPassword(),
            'password_salt' => $member->getEncodedPassword()->getSalt(),
            'roles' => implode(',', $member->getRoles()),
        ]);
    }

    public function findByEmail(Email $email): Member
    {
        $query =<<<SQL
SELECT * FROM members WHERE email = :email LIMIT 1;
SQL;

        $statement = $this->connection->prepare($query);
        $statement->execute(['email' => (string) $email]);
        $row = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {

            throw MemberNotFound::unknownEmail($email);
        }

        return $this->hydrateFromRow($row);
    }

    private function hydrateFromRow(array $row): Member
    {
        return Member::fromArray($row);
    }
}
