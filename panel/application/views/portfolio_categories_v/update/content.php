<div class="row">
    <div class="col-md-12">
		<h4 class="m-b-lg">
        <?php echo "<b>$item->title</b> kaydını düzenliyorsunuz"; ?>
        </h4>
	</div>
    <div class="col-md-12">
        <div class="widget">
            <div class="widget-body">
                <form action="<?php echo base_url("portfolio_categories/update/$item->id"); ?>" method="post">
                    <div class="form-group">
                        <label>Başlık</label>
                        <input class="form-control" placeholder="Başlık" name="title" value="<?php echo $item->title; ?>">
                        <?php if(isset($form_error)){ ?>
                            <small class="pull-right input-form-error"> <?php echo form_error("title");?></small>
                        <?php } ?>
                    </div>

                    <button type="submit" class="btn btn-primary btn-md btn-outline">Güncelle</button>
                    <a href="<?php echo base_url("portfolio_categories"); ?>" class="btn btn-danger btn-outline">İptal</a>
                </form>
            </div><!-- .widget-body -->
        </div>
	</div>
</div>