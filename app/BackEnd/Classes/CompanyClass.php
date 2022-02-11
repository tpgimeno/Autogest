<?php

namespace App\BackEnd\Classes;

use App\Models\Company;

class CompanyClass{
    protected int $id;
    protected string $name;
    protected string $fiscalId;
    protected string $address;
    protected int $postalCode;
    protected string $city;
    protected string $state;
    protected string $country;
    protected string $email;
    protected string $site;
    protected string $telephone;
    
    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getFiscalId(): string {
        return $this->fiscalId;
    }

    public function getAddress(): string {
        return $this->address;
    }

    public function getPostalCode(): int {
        return $this->postalCode;
    }

    public function getCity(): string {
        return $this->city;
    }

    public function getState(): string {
        return $this->state;
    }

    public function getCountry(): string {
        return $this->country;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getSite(): string {
        return $this->site;
    }

    public function getTelephone(): string {
        return $this->telephone;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function setFiscalId(string $fiscalId): void {
        $this->fiscalId = $fiscalId;
    }

    public function setAddress(string $address): void {
        $this->address = $address;
    }

    public function setPostalCode(int $postalCode): void {
        $this->postalCode = $postalCode;
    }

    public function setCity(string $city): void {
        $this->city = $city;
    }

    public function setState(string $state): void {
        $this->state = $state;
    }

    public function setCountry(string $country): void {
        $this->country = $country;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setSite(string $site): void {
        $this->site = $site;
    }

    public function setTelephone(string $telephone): void {
        $this->telephone = $telephone;
    }
    public function getAllRegisters(){
        $companies = Company::All();
        return $companies;
    }
    public function searchCompanies($searchString){
        $companies = Company::Where("id", "like", "%".$searchString."%")
                ->orWhere("name", "like", "%".$searchString."%")
                ->orWhere("fiscalName", "like", "%".$searchString."%")
                ->orWhere("fiscalId", "like", "%".$searchString."%")
                ->orWhere("phone", "like", "%".$searchString."%")
                ->orWhere("email", "like", "%".$searchString."%")
                ->get(); 
        if($companies){
            return $companies;
        }else{
            return null;
        }
    }

}

