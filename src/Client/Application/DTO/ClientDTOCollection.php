<?php

namespace App\Client\Application\DTO;

use Doctrine\Common\Collections\ArrayCollection;

class ClientDTOCollection extends ArrayCollection
{
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);
    }
}
