<?php

namespace App\Platform\Golem;

/**
 * Golem or fake Objects for processing the data.
 *
 * @author kamran
 */
class OrderGolem
{

  protected $orderDirection; // Forward or revers etc
  protected $orderID;
  protected $invoiceNumber;
  protected $orderDate;
  protected $customerName;
  protected $customerMobileNumber;
  protected $customerAlternateNumber;
  protected $customerAddress;
  protected $customerCity;
  protected $customerCountry;
  protected $customerPhoneNumber;
  protected $customerArea;
  protected $customerPincode;
  protected $cashOnDeliveryAmount;
  protected $orderTotalAmount;
  protected $goodsDescription;
  protected $specialInstructions;
  protected $paymentMethod; // Cod/CC/Paypal etc
  protected $customerEmail;
  protected $store;
  protected $totalItemsQuantity;
  protected $toCompany;
  protected $companyName = 'Business Arcade.com';
  protected $companyName2 = 'DressFair dressfair.com';
  protected $companyAddress = 'Sharjah industrial area 11, beside al Jameel granite shed number 6';
  protected $companyCity = 'SHARJAH';
  protected $companyCountry = 'UAE';
  protected $companyBuilding = 'Dubai';
  protected $companyMobileNumber = '971';
  protected $orderItems = array();

  function getStore(){
    return $this->store;
  }
  function getCustomerEmail()
  {
    return $this->customerEmail;
  }

  function setCustomerEmail($customerEmail)
  {
    $this->customerEmail = $customerEmail;
  }

  function getOrderDirection()
  {
    return $this->orderDirection;
  }

  function getOrderID()
  {
    return $this->orderID;
  }
  
  function getInvoiceNumber()
  {
    return $this->invoiceNumber;
  }

  function getOrderDate()
  {
    return $this->orderDate;
  }

  function getCustomerName()
  {
    return $this->customerName;
  }

  function getCustomerMobileNumber()
  {
    return $this->customerMobileNumber;
  }
  function getCustomerAlternateNumber(){
    return $this->customerAlternateNumber;
  }
  function getCustomerAddress()
  {
    return $this->customerAddress;
  }

  function getCustomerCity()
  {
    return $this->customerCity;
  }
  function getCustomerCountry()
  {
    return $this->customerCountry;
  }

  function getCustomerPhoneNumber()
  {
    return $this->customerPhoneNumber;
  }

  function getCustomerArea()
  {
    return $this->customerArea;
  }

  function getCustomerPincode()
  {
    return $this->customerPincode;
  }

  function getCashOnDeliveryAmount()
  {
    return $this->cashOnDeliveryAmount;
  }

  function getOrderTotalAmount()
  {
    return $this->orderTotalAmount;
  }

  function getGoodsDescription()
  {
    return $this->goodsDescription;
  }

  function getSpecialInstructions()
  {
    return $this->specialInstructions;
  }

  function getPaymentMethod()
  {
    return $this->paymentMethod;
  }
  function getToCompany()
  {
    return $this->toCompany;
  }
  function getCompanyName()
  {
    return $this->companyName;
  }
  function getCompanyName2()
  {
    return $this->companyName2;
  }
  function getCompanyAddress()
  {
    return $this->companyAddress;
  }
  function getCompanyBuilding()
  {
    return $this->companyBuilding;
  }
  function getCompanyCity()
  {
    return $this->companyCity;
  }
  function getCompanyCountry()
  {
    return $this->companyCountry;
  }
  function getCompanyMobileNumber()
  {
    return $this->companyMobileNumber;
  }
  function getOrderItems()
  {
    return $this->orderItems;
  }
  
  function setOrderDirection($orderDirection)
  {
    $this->orderDirection = $orderDirection;
  }
  function setStore($store_id){
    $this->store = $store_id;
  }
  function setOrderID($orderID)
  {
    $this->orderID = $orderID;
  }

  function setInvoiceNumber($invoiceNumber)
  {
    $this->invoiceNumber = $invoiceNumber;
  }

  function setOrderDate($orderDate)
  {
    $this->orderDate = $orderDate;
  }

  function setCustomerName($customerName)
  {
    $this->customerName = $customerName;
  }

  function setCustomerMobileNumber($customerMobileNumber)
  {
    $this->customerMobileNumber = $customerMobileNumber;
  }
  function setCustomerAlternateNumber($customerAlternateNumber){
    return $this->customerAlternateNumber = $customerAlternateNumber;
  }
  function setCustomerAddress($customerAddress)
  {
    $this->customerAddress = trim($customerAddress);
  }

  function setCustomerCity($customerCity)
  {
    $this->customerCity = trim($customerCity);
  }
  function setCustomerCoutry($customerCountry)
  {
    $this->customerCountry = trim($customerCountry);
  }
  function setCustomerPhoneNumber($customerPhoneNumber)
  {
    $this->customerPhoneNumber = $customerPhoneNumber;
  }

  function setCustomerArea($customerArea)
  {
    $this->customerArea = trim($customerArea);
  }

  function setCustomerPincode($customerPincode)
  {
    $this->customerPincode = trim($customerPincode);
  }

  function setCashOnDeliveryAmount($cashOnDeliveryAmount)
  {
    $this->cashOnDeliveryAmount = $cashOnDeliveryAmount;
  }

  function setOrderTotalAmount($orderTotalAmount)
  {
    $this->orderTotalAmount = $orderTotalAmount;
  }

  function setGoodsDescription($goodsDescription)
  {
    $this->goodsDescription = trim($goodsDescription);
  }

  function setSpecialInstructions($specialInstructions)
  {
    if ("" == $specialInstructions)
    {
      $this->specialInstructions = "N/A";
      return;
    }
    $this->specialInstructions = trim($specialInstructions);
  }

  function setPaymentMethod($paymentMethod)
  {
    $this->paymentMethod = $paymentMethod;
  }

  function getTotalItemsQuantity()
  {
    return $this->totalItemsQuantity;
  }

  function setTotalItemsQuantity($totalItemsQuantity)
  {
    $this->totalItemsQuantity = $totalItemsQuantity;
  }
  function setToCompany($toCompany)
  {
    $this->toCompany = $toCompany;
  }
  function setOrderItems($orderItems)
  {
    $this->orderItems = $orderItems;
  }

}
