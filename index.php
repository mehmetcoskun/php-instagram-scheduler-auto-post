<?php require_once "includes/header.php"; ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Gösterge Paneli
                <small>Panel</small>
            </h1>
        </section>

        <!-- Main content -->
        <section class="content container-fluid">

            <?php
            $schedules = $db->prepare("SELECT * FROM schedules WHERE username = ?");
            $schedules->execute([$_SESSION["username"]]);
            if ($schedules->rowCount()):
                ?>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-warning">
                            <div class="box-header">
                                <h3 class="box-title">Planlı Gönderilerim</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body table-responsive no-padding">
                                <table class="table table-hover">
                                    <tr>
                                        <th>Medya İsimleri</th>
                                        <th>Açıklama</th>
                                        <th>Tarih</th>
                                        <th>Saat</th>
                                        <th>Durum</th>
                                    </tr>
                                    <?php while ($schedulesget = $schedules->fetch(PDO::FETCH_ASSOC)): ?>
                                        <tr>
                                            <td>
                                                <?php for ($i = 0; $i < sizeof(explode(",", $schedulesget["mediasname"])); $i++): ?>
                                                    <a href="system/userfiles/<?= explode(",", $schedulesget["mediasname"])[$i] ?>" target="_blank"><?= explode(",", $schedulesget["mediasname"])[$i] ?></a>
                                                <?php endfor; ?>
                                            </td>
                                            <td><?= $schedulesget["description"] ?></td>
                                            <td><?= $schedulesget["date"] ?></td>
                                            <td><span class="label label-warning"><?= $schedulesget["time"] ?></span></td>
                                            <td><span class="label label-<?php
                                                if ($schedulesget["status"] == "success") {
                                                    echo "success";
                                                } elseif ($schedulesget["status"] == "waiting") {
                                                    echo "warning";
                                                } elseif ($schedulesget["status"] == "error") {
                                                    echo "danger";
                                                }
                                                ?>">
                                                <?php
                                                if ($schedulesget["status"] == "success") {
                                                    echo "Başarılı";
                                                } elseif ($schedulesget["status"] == "waiting") {
                                                    echo "Bekleniyor...";
                                                } elseif ($schedulesget["status"] == "error") {
                                                    echo "Hata";
                                                }
                                                ?>
                                            </span></td>
                                            <td><button class="btn btn-default" name="scheduledelete" id="<?=encryptId($schedulesget['id'])?>"><i class="fa fa-remove"></i></button></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </table>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    Daha önce hiç gönderi planlanamadın.
                </div>
            <?php endif; ?>

        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
<?php require_once "includes/footer.php"; ?>