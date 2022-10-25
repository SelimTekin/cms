<?php

// bu fonksiyonu her sayfada kullanabiliriz. Bundan dolayı
function convertToSEO($text){

    $turkce = array("ç", "Ç", "ğ", "Ğ", "ü", "Ü", "ö", "Ö", "ı", "İ", "ş", "Ş", ".", ",", "!", "'", "\"", " ", "?", "*", "_", "|", "=", "(", ")", "[", "]", "{", "}");
    $convert = array("c", "c", "g", "g", "u", "u", "o", "o", "i", "i", "s", "s", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-");

    return strtolower(str_replace($turkce, $convert, $text)); # test'te bulduğu turkce'deki indeksleri convert'te denk gelenlerle değiştirir.
}

function getFileName($id){

    /* 
        Bu ÇALIŞMAZ çünkü helper içerisinde codeigniter çalışmadığından dolayı $this anahatar kelimesini kullanamayız. Çünkü Helper içi herhangi bir
        OOP içermiyor(Düz PHP fonksiyonu). Çalışması için $this'i elde etmemiz gerekiyor. Bunun için ise Codeigniter'ın bir instance'ını almamız gerekir.
        Bunu yapmak için $t = &gfet_instance(); yaparak $t değişkenini artık $this yerine kullanabiliriz.
    */
    // $fileName = $this->product_image_model->get(
    //     array(
    //         "id"    => $id
    //     )
    // );

    $t = &get_instance();

    $fileName = $t->product_image_model->get(
        array(
            "id"    => $id
        )
    );

    // Bu şekilde de olur ($myModel'i bu fonksiyonun bir paramtresi olarak yazıp Product.php dosyasında parametrenin değerini($this->product_image_model) verebiliriz)
    // $fileName = $myModel->get(
    //     array(
    //         "id"    => $id
    //     )
    // );

    return $fileName->img_url;
}