$(document).ready(function(){

    $(".sortable").sortable();

    $(".remove-btn").click(function(){

        var $data_url = $(this).data("url"); // başında data- olan attributeleri keywordleri buluyor

        Swal.fire({
            title: 'Emin misiniz?',
            text: "Bu işlemi geri alamayacaksınız!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Evet, Sil!',
            cancelButtonText: 'Hayır'
          }).then(function(result){
            if (result.isConfirmed) {
              window.location.href = $data_url;
            }
          });
    });

    $(".isActive").change(function(){

      var $data = $(this).prop("checked");
      var $data_url = $(this).data("url");

      if(typeof $data !== "undefined" && typeof $data_url !== "undefined"){
        
        $.post($data_url, {data: $data}, function(response){ // jquery'nin post metodu içerisine varsayılan olarak 3 tane parametre alır(url, obje türüdne bir nesne(input name'i), callback function(handle edicek fonksiyon. Mesela $data_url'den gelecek cevap(echo $id)))
                     
        });

      }

    });

    $(".sortable").on("sortupdate", function(event, ui){
      
      var $data = $(this).sortable("serialize");
      var $data_url = $(this).data("url")
      
      $.post($data_url, {data: $data}, function(response){})

    });

    var uploadSection = Dropzone.forElement("#dropzone"); // Dropzone plugin'ini kullanarak forElement metdouyla id'si dropzone olan elementin instance'ını uploadSection değişkeni altında kullanma

    uploadSection.on("complete", function(file){ // uploadSection complete olduğunda bir tane callback function çağırma
      
      var $data_url = $("#dropzone").data("url");

      $.post($data_url, {}, function(response){ // parametreler -> birincisi - hangi url'ye post yapıcam, ikincisi - hangi bilgileri göndericem, üçüncüsü - bir cevap geldiğinde bunu nasıl handle edicem
        
        $(".image_list_container").html(response);

        $('[data-switchery]').each(function(){
          var $this = $(this),
              color = $this.attr('data-color') || '#188ae2',
              jackColor = $this.attr('data-jackColor') || '#ffffff',
              size = $this.attr('data-size') || 'default'
  
          new Switchery(this, {
            color: color,
            size: size,
            jackColor: jackColor
          });
        });

      })

    })

})