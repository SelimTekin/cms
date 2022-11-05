<div class="row">
    <div class="col-md-12">
		<h4 class="m-b-lg">
            <?php echo "<b>$gallery->title</b> galerisine ait videolar"; ?>
            <a href="<?php echo base_url("galleries/new_gallery_video_form/$gallery->id"); # galleries controller'ı altındaki new_video_form metdou çağır ?>" class="btn btn-outline btn-primary btn-xs pull-right"><i class="fa fa-plus"></i> Yeni Ekle</a>
        </h4>
	</div>
    <div class="col-md-12">
		<div class="widget p-lg">

            <?php if(empty($items)){ ?>
                <div class="alert alert-info text-center">
                    <p>Burada herhangi bir kayıt bulunamadı. Eklemek için lütfen <a href="<?php echo base_url("galleries/new_gallery_video_form/$gallery->id"); # galleries controller'ı altındaki new_video_form metdou çağır ?>">tıklayınız.</a></p>
                </div>
            <?php } else{ ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered content-container">
                        <thead>
                            <th class="order"><i class="fa fa-reorder"></i></th>
                            <th class="w50">#id</th>
                            <th>Url</th>
                            <th>Görsel</th>
                            <th>Durumu</th>
                            <th>İşlem</th>
                        </thead>
                        <tbody class="sortable" data-url="<?php echo base_url("galleries/rankGalleryVideoSetter");?>">
                            <?php foreach($items as $item){ ?>
                                <tr id="ord-<?php echo $item->id; ?>">
                                    <td class="order"><i class="fa fa-reorder"></i></td>
                                    <td class="w50 text-center">#<?php echo $item->id; ?></td>
                                    <td class="text-center"><?php echo $item->url; ?></td>
                                    <td class="text-center w100">
                                        <iframe 
                                            width="200" 
                                            src="<?php echo $item->url; ?>" 
                                            title="YouTube video player" 
                                            frameborder="0" 
                                            gesture="media  "
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                            allowfullscreen>
                                        </iframe>                                        
                                    </td>
                                    <td class="text-center w100">
                                            <input
                                                data-url="<?php echo base_url("galleries/galleryVideoIsActiveSetter/$item->id") ?>"
                                                class="isActive" 
                                                id="switch-2-2" 
                                                type="checkbox"
                                                data-switchery
                                                data-color="#10c469"
                                                <?php echo ($item->isActive) ? "checked" : ""; ?>
                                            />
                                    </td>
                                    <td class="text-center w200">
                                        <button 
                                            data-url="<?php echo base_url("galleries/galleryVideoDelete/$item->id/$item->gallery_id"); # id'yi parametre olarak verdik ?>"
                                            class="btn btn-sm btn-danger btn-outline remove-btn">
                                            <i class="fa fa-trash"></i> Sil
                                        </button>
                                        <a href="<?php echo base_url("galleries/update_gallery_video_form/$item->id"); # id'yi parametre olarak verdik ?>" class="btn btn-sm btn-info btn-outline"><i class="fa fa-pencil-square-o"></i> Düzenle</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
		</div><!-- .widget -->
	    </div>
</div>