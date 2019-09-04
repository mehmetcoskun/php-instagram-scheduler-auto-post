<?php require_once "includes/header.php"; ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Gönderi Planla
                <small>Gönderi</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content container-fluid">

            <div id="info"></div>

            <!-- SELECT2 EXAMPLE -->
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Yeni Gönderi Oluştur</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                <form id="createschedule" enctype="multipart/form-data">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="media">Medya</label>
                            <input type="file" name="media[]" id="media" multiple>
                        </div>
                        <div class="form-group">
                            <label>Açıklama</label>
                            <textarea class="form-control" rows="3" name="description" placeholder="Açıklama"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="date">Tarih</label>
                            <input type="date" class="form-control" name="date" id="date">
                        </div>
                        <div class="form-group">
                            <label for="time">Saat</label>
                            <input type="time" class="form-control" name="time" id="time">
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Gönder</button>
                        <input type="hidden" name="createschedule_hidden">
                    </div>
                </form>
            </div>
            <!-- /.box -->

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
<?php require_once "includes/footer.php"; ?>