<?php
namespace Ecommerce\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Ecommerce\PaymentBundle\Entity\BankAccountRepository")
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
     * @ORM\Column(name="iban", type="string", length=4)
     */
    private $iban;

    /**
     * @ORM\Column(name="bankCode", type="string", length=4)
     */
    private $bankCode;

    /**
     * @ORM\Column(name="branchCode", type="string", length=4)
     */
    private $branchCode;

    /**
     * @ORM\Column(name="checkDigits", type="string", length=2)
     */
    private $checkDigits;

    /**
     * @ORM\Column(name="accountNumber", type="string", length=10)
     */
    private $accountNumber;

    /**
     * @ORM\Column(name="bankName", type="string", nullable=true)
     */
    private $bankName;

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
        $ccc = $this->bankCode . ' ' . $this->branchCode . ' ' . $this->checkDigits . ' ' . $this->accountNumber;
        if (isset($this->iban)) {
            $ccc = $this->iban . ' ' . $ccc;
        }
        return $ccc;
    }

    /**
     * @return mixed
     */
    public function getBankName()
    {
        return $this->bankName;
    }

    /**
     * @param mixed $bankName
     */
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;
    }

    public function getBankAccount() {
        return str_replace(' ', '', $this->__toString());
    }
}
