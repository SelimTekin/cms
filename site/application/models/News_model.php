<?php

class News_model extends CI_Model{

    public $tableName = "news";

    public function __construct(){

        parent::__construct();

    }

    // Tüm kayıtları getirecek olan metot
    public function get_all($where = array(), $order = "id ASC"){ # tek şart olarak id'yi kullanmayabiliriz bu yüzden birden fazla şartı kabul etmesi için array'e dönüştürdük. order ise hiçbir şey girilmezse id'yi ascending olarak getirsin yani sıralı

        return $this->db->where($where)->order_by($order)->get($this->tableName)->result();

    }

    // Tek bir kayıt getirecek olan metot (o yüzden result yerine row yazdık)
    public function get($where = array()){ # tek şart olarak id'yi kullanmayabiliriz bu yüzden birden fazla şartı kabul etmesi için array'e dönüştürdük

        return $this->db->where($where)->get($this->tableName)->row();

    }

    public function add($data = array()){
        return $this->db->insert($this->tableName, $data);
    }

    public function update($where = array(), $data = array()){
        return $this->db->where($where)->update($this->tableName, $data); # nerede neyi kaydedicez
    }

    public function delete($where = array()){
        return $this->db->where($where)->delete($this->tableName);
    }

}