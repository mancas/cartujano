<?php
namespace Ecommerce\PaymentBundle\Entity;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class BankAccount
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="iban", type="integer", nullable=true)
     */
    private $iban;

    /**
     * @ORM\Column(name="bankCode", type="integer")
     */
    private $bankCode;

    /**
     * @ORM\Column(name="branchCode", type="integer")
     */
    private $branchCode;

    /**
     * @ORM\Column(name="checkDigits", type="integer")
     */
    private $checkDigits;

    /**
     * @ORM\Column(name="accountNumber", type="integer")
     */
    private $accountNumber;

    /**
     * @return mixed
     */
    public function getIban()
    {
        return $this->iban;
    }

    /**
     * @param mixed $iban
     */
    public function setIban($iban)
    {
        $this->iban = $iban;
    }

    /**
     * @return mixed
     */
    public function getBankCode()
    {
        return $this->bankCode;
    }

    /**
     * @param mixed $bankCode
     */
    public function setBankCode($bankCode)
    {
        $this->bankCode = $bankCode;
    }

    /**
     * @return mixed
     */
    public function getBranchCode()
    {
        return $this->branchCode;
    }

    /**
     * @param mixed $branchCode
     */
    public function setBranchCode($branchCode)
    {
        $this->branchCode = $branchCode;
    }

    /**
     * @return mixed
     */
    public function getCheckDigits()
    {
        return $this->checkDigits;
    }

    /**
     * @param mixed $checkDigits
     */
    public function setCheckDigits($checkDigits)
    {
        $this->checkDigits = $checkDigits;
    }

    /**
     * @return mixed
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @param mixed $accountNumber
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    function __toString()
    {
        $ccc = $this->bankCode + ' ' + $this->branchCode + ' ' + $this->checkDigits + ' ' + $this->accountNumber;
        if (isset($this->iban)) {
            $ccc = $this->iban + ' ' + $ccc;
        }
        return $ccc;
    }

    public function getBankAccount() {
        return str_replace(' ', '', $this->__toString());
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('bankAccount', new NotBlank());
        $metadata->addPropertyConstraint('name', new ContainsAlphanumeric());
    }
}
