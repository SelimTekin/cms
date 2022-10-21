$(document).ready(function(){

    $(".remove-btn").click(function(e){

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

    // Swal.fire({
    //     title: 'Error!',
    //     text: 'Do you want to continue',
    //     icon: 'error',
    //     confirmButtonText: 'Cool'
    // })
})