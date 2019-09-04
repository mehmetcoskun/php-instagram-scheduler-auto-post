## Instagram Scheduler & Auto Post
![NetrassCloud Logo](images/logo.png) ![Instagram Logo](images/instagram.png)

Netrassgram Web Tabanlı bir Instagram zamanlama sistemidir. Instagram gönderilerinizi belirlerdiğiniz zamanda paylaşır. yönetim panelinden gönderilerinizi yönetip yeni gönderi zamanlayabilirsiniz.

**Geliştirilmeye açıktır.**

### Ekran Görüntüleri

![Page 1](images/page1.png)

![Page 2](images/page2.png)

![Page 3](images/page3.png)

![Page 4](images/page4.png)

### Kurulum

 - "system" klasörü içerisinde database.json dosyasını kendinize göre düzenleyin.
 - Netrassgram sınıfında bulunan 93. ve 94. satırlarlarını kendinize göre düzenleyin. (FFmpeg ve FFprobe yolunu belirtmelisiniz)
 - CPanel üzerinden cronjob ayarlarını yapın (dakikada birkez) veya [cron-job.org](https://cron-job.org/) adresinden check.php'ye istek gönderin
 - [netrassgram.sql](netrassgram.sql) dosyasını veritabanınıza aktarın.

composer kurulumunuda tamamladıktan yapmanız gereken sadece siteye giriş yapmak.
