<?php 

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserDTO
{
    public function __construct(
        #[Assert\Length(min: 2, max: 100)]
        public string $firstName,

        #[Assert\Length(min: 2, max: 100)]
        public string $lastName,

        #[Assert\Email(mode: 'strict')]
        public string $email,
    ) {
    }
}