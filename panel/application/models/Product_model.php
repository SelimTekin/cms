<?php

class Product_model extends CI_Model{

    public $tableName = "products";

    public function __construct(){

        parent::__construct();

    }

    // Tüm kayıtları getirecek olan metot
    public function get_all($where = array()){ # tek şart olarak id'yi kullanmayabiliriz bu yüzden birden fazla şartı kabul etmesi için array'e dönüştürdük

        return $this->db->where($where)->get($this->tableName)->result();

    }

    // Tek bir kayıt getirecek olan metot (o yüzden result yerine row yazdık)
    public function get($where = array()){ # tek şart olarak id'yi kullanmayabiliriz bu yüzden birden fazla şartı kabul etmesi için array'e dönüştürdük

        return $this->db->where($where)->get($this->tableName)->row();

    }

    public function add($data = array()){
        return $this->db->insert($this->tableName, $data);
    }

}