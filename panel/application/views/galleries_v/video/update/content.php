<div class="row">
    <div class="col-md-12">
		<h4 class="m-b-lg">
        <?php echo "<b>Video</b> kaydını düzenliyorsunuz"; ?>
        </h4>
	</div>
    <div class="col-md-12">
        <div class="widget">
            <div class="widget-body">
                <form action="<?php echo base_url("galleries/gallery_video_update/$item->id/$item->gallery_id"); ?>" method="post"> <!--Eğer formda bir dosya göndermek istiyorsak enctype="multipart/data" dememiz lazım ki $_FILES isimli değişkeni(galleries.php controller'ında) doldurabilsin yoksa boş array döner-->

                    <div class="form-group">
                        <label>Video URL</label>
                        <input class="form-control" placeholder="Video bağlantısını buraya yapıştırınız" name="url" value="<?php echo $item->url; ?>">
                        <?php if(isset($form_error)){ ?>
                            <small class="pull-right input-form-error"> <?php echo form_error("url");?></small>
                        <?php } ?>
                    </div>

                    <button type="submit" class="btn btn-primary btn-md btn-outline">Güncelle</button>
                    <a href="<?php echo base_url("galleries/gallery_video_list/$item->gallery_id"); ?>" class="btn btn-danger btn-outline">İptal</a>
                </form>
            </div><!-- .widget-body -->
        </div>
	</div>
</div>