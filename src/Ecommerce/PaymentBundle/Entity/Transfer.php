<?php
namespace Ecommerce\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table()
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks
 */
class Transfer extends Payment
{
    const REQUESTED = 'solicitando';
    const PAID ="pagado";

    /**
     * @ORM\Column(name="state", type="string")
     */
    private $state;

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function isPaid() {
        return $this->state == self::PAID;
    }
}