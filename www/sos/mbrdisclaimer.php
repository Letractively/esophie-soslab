<?include "mbrheader.php";?>
	<p>Sebelum anda bisa melakukan order online, <br>
	<font class="fontpink">Persyaratan</font> dibawah ini harus anda setujui terlebih dahulu:</p>
	
	<ol class="disclaimerlist">
            <li>Konsumen yang order via online order wajib sudah menjadi member sebelumnya 
            <em>(Online orders can only be made by members.)</em></li>

            <li>Member wajib memberikan data yang benar sesuai data dan identitas resmi di kantor pusat 
            <em>(Members must give the exact information and identity that the HQ have.)</em></li>

            <li>Member wajib mengisi nama BC untuk pengiriman barang online order 
            <em>(Members must fill in the BC's name for online orders deliveries.)</em></li>

            <li>Member berhak atas point bonus atas barang yang dibeli via online order.
            <em>(Members are entitled to bonus points gained from purchasing items online.)</em></li>
            
            <li>Point bonus hanya bisa dilaporkan setelah faktur untuk order online telah diterima dari BC.
            <em>(Bonus points can only be reported after the order invoice has been delivered by the BC to the member)</em></li>

            <li>Member berhak atas after sales service sesuai ketentuan yang berlaku 
            <em>(Members are entitled to an after sales service corresponding to the applicable rules.)</em></li>

            <li>Member berhak mendapatkan invoice asli yang diberikan oleh BC atas pemesanan barang 
            <em>(Members are entitled to an original invoice provided by the BC after an order.)</em></li>

            <li>Member tidak dapat membatalkan pesanan setelah proses pembayaran dilakukan 
            <em>(Members can't cancel an order after the payment has been processed.)</em></li>

            <li>Dengan mengisi data pemesanan maka member dianggap mengetahui dan menyetujui syarat dan ketentuan online order 
            <em>(By filling in the order information, members are considered like they have read and accepted the terms and conditions of online orders.)</em></li>

            <li>Member wajib memberitahukan nomor handphone. Jika SMS tidak diterima, member bertanggung jawab untuk memeriksa sendiri status pesanan di sophie mobile. 
            <em>(Members must give their mobile phone number. If they don't receive an SMS, it is their own responsibility to check their order's status on Sophie Mobile.)</em></li>

            <li>Setelah member mengirim ke BC, pesanan akan divalidasi dan tidak dapat diubah lagi oleh member. Orderan ini dapat ditolak atau dibatalkan tanpa meminta persetujuan member terlebih dahulu. 
            <em>(After sending an SMS to a BC, the order is placed and can't be modified by the member. This is an "offering" from the member and not an official rule. Sophie Paris and their affiliates are not responsible in any way to fulfill the order. This matter can be rejected and cancelled without asking the member.)</em></li>

            <li>Barang tidak akan disediakan sebelum BC dan kantor pusat validasi. 
            <em>(Goods will not be provided before the BC and HQ validation.)</em></li>

            <li>Member akan menerima respon dari BC / Sophie dalam waktu maksimum 60 menit jika order diproses selama waktu operasional BC, atau hari kerja berikutnya, jam kerja operasional BC ditambah 1 jam. 
            <em>(Members will receive a response from a BC/Sophie before 60 minutes if the order is placed during the BC's working hours or the next business day, the opening hours of a BC: +1 hour.)</em></li>

            <li>Cara pembayaran dipilih sebelum penempatan order dan tidak dapat dirubah. 
            <em>(Type of payment is chosen before placing an order and it won't be able to be changed.)</em></li>

            <li>Order akan dibatalkan jika tidak ada pembayaran sebelum cut-off waktu pembayaran. 
            <em>(The order will be cancelled if there isn't an upcoming payment before the payment cut-off time.)</em></li>

            <li>Cut-off pembayaran dihitung mulai dari BC / Sophie validasi pesanan, +1:30 jam untuk ATM, +1 jam untuk cara pembayaran lainnya. 
            <em>(The payment cut-off time is calculated from the BC/Sophie confirms the order, +1:30 hours for ATM, +1 hour for any other types of payment.)</em></li>

            <li>Jika member tidak complain dalam waktu 24 jam setelah pesanan divalidasi oleh BC dan tidak ada konfirmasi pembayaran diterima di Sophie HQ maka orderan dianggap batal
            <em>(If the member doesn't complain 24 hours after the order confirmation by the BC, it will be considered as cancelled if the HQ hasn't received any payment confirmation.)</em></li>

            <li>Jika ada masalah pembayaran, member harus memberikan bukti pembayaran yang sudah berhasil untuk penggantian atau pengembalian uang
            <em>(If there is any problem related to the payment, the member must give a proof of payment for a refund.)</em></li>

            <li>Pembayaran akan direconsil oleh HQ Sophie selama jam kerja Kasir. Order dianggap final setelah validasi pembayaran oleh Kasir Sophie HQ. 
            <em>(The HQ will reconcile payment during the working hours of the Cashier. Sophie Paris Cashier considers order as accepted and final only after validation of the payment.)</em></li>
        </ol>
	
	<?if($ctrl->firstlogin) { ?>
		<input type="checkbox" name="agree" id="agree" value="1"> Saya setuju dengan persyaratan diatas.
		<? if($ctrl->varvalue("errmsg") != '') { ?><br><span class="errmsg"><?=$ctrl->varvalue("errmsg")?></span><?}?>
		<br><button type="button" onclick="setaction('setuju');">Setuju</button>
	<? } else { ?>
		<button type="button" onclick="setaction('lanjut');">Lanjut >></button>
	<? } ?>
	
	
<?include "mbrfooter.php";?>