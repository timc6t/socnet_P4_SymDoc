<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'follows')]
class Follows
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $followId;

    #[ORM\Column(type: 'integer')]
    private $followerId;

    #[ORM\Column(type: 'integer')]
    private $followingId;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $userId;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    public function getFollowId() {
        return $this->followId;
    }

    public function getFollowerId() {
        return $this -> followerId;
    }

    public function setFollowerId(int $followerId) {
        $this -> followerId = $followerId;
    }

    public function getFollowingId() {
        return $this -> followingId;
    }

    public function setFollowingId($followingId) {
        $this -> followingId = $followingId;
    }
}
