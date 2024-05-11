<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactDTO
{
    #[NotBlank]
    #[Length(min: 3, max: 200)]
    public $name = '';

    #[NotBlank]
    #[Email]
    public $email = '';

    #[NotBlank]
    #[Length(min: 3, max: 200)]
    public $message = '';


    #[NotBlank]
    public $service = '';

}