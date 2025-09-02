<?php

namespace App\Dto;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterDto
{
    #[Assert\Length(min: 2, max: 100)]
    public ?string $firstname = null;

    #[Assert\Length(min: 2, max: 100)]
    public ?string $lastname = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    public string $password;
}
