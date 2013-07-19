<?include "mbrheader.php";?>
	<p>Sebelum anda bisa melakukan order online, <br>
	<font class="pink">Persyaratan</font> dibawah ini harus anda setujui terlebih dahulu:</p>
	
	<ol class="disclaimerlist">
            <li>Konsumen yang order via online order wajib sudah menjadi member sebelumnya 
            <font class="italicpink">(Online orders can only be made by members.)</font></li>

            <li>Member wajib memberikan data yang benar sesuai data dan identitas resmi di kantor pusat 
            <font class="italicpink">(Members must give the exact information and identity that the HQ have.)</font></li>

            <li>Member wajib mengisi nama BC untuk pengiriman barang online order 
            <font class="italicpink">(Members must fill in the BC's name for online orders deliveries.)</font></li>

            <li>Member berhak atas point bonus atas barang yang dibeli via online order.
            <font class="italicpink">(Members are entitled to bonus points gained from purchasing items online.)</font></li>
            
            <li>Point bonus hanya bisa dilaporkan setelah faktur untuk order online telah diterima dari BC.
            <font class="italicpink">(Bonus points can only be reported after the order invoice has been delivered by the BC to the member)</font></li>

            <li>Member berhak atas after sales service sesuai ketentuan yang berlaku 
            <font class="italicpink">(Members are entitled to an after sales service corresponding to the applicable rules.)</font></li>

            <li>Member berhak mendapatkan invoice asli yang diberikan oleh BC atas pemesanan barang 
            <font class="italicpink">(Members are entitled to an original invoice provided by the BC after an order.)</font></li>

            <li>Member tidak dapat membatalkan pesanan setelah proses pembayaran dilakukan 
            <font class="italicpink">(Members can't cancel an order after the payment has been processed.)</font></li>

            <li>Dengan mengisi data pemesanan maka member dianggap mengetahui dan menyetujui syarat dan ketentuan online order 
            <font class="italicpink">(By filling in the order information, members are considered like they have read and accepted the terms and conditions of online orders.)</font></li>

            <li>Member wajib memberitahukan nomor handphone. Jika SMS tidak diterima, member bertanggung jawab untuk memeriksa sendiri status pesanan di sophie mobile. 
            <font class="italicpink">(Members must give their mobile phone number. If they don't receive an SMS, it is their own responsibility to check their order's status on Sophie Mobile.)</font></li>

            <li>Setelah member mengirim ke BC, pesanan akan divalidasi dan tidak dapat diubah lagi oleh member. Orderan ini dapat ditolak atau dibatalkan tanpa meminta persetujuan member terlebih dahulu. 
            <font class="italicpink">(After sending an SMS to a BC, the order is placed and can't be modified by the member. This is an "offering" from the member and not an official rule. Sophie Paris and their affiliates are not responsible in any way to fulfill the order. This matter can be rejected and cancelled without asking the member.)</font></li>

            <li>Barang tidak akan disediakan sebelum BC dan kantor pusat validasi. 
            <font class="italicpink">(Goods will not be provided before the BC and HQ validation.)</font></li>

            <li>Member akan menerima respon dari BC / Sophie dalam waktu maksimum 60 menit jika order diproses selama waktu operasional BC, atau hari kerja berikutnya, jam kerja operasional BC ditambah 1 jam. 
            <font class="italicpink">(Members will receive a response from a BC/Sophie before 60 minutes if the order is placed during the BC's working hours or the next business day, the opening hours of a BC: +1 hour.)</font></li>

            <li>Cara pembayaran dipilih sebelum penempatan order dan tidak dapat dirubah. 
            <font class="italicpink">(Type of payment is chosen before placing an order and it won't be able to be changed.)</font></li>

            <li>Order akan dibatalkan jika tidak ada pembayaran sebelum cut-off waktu pembayaran. 
            <font class="italicpink">(The order will be cancelled if there isn't an upcoming payment before the payment cut-off time.)</font></li>

            <li>Cut-off pembayaran dihitung mulai dari BC / Sophie validasi pesanan, +1:30 jam untuk ATM, +1 jam untuk cara pembayaran lainnya. 
            <font class="italicpink">(The payment cut-off time is calculated from the BC/Sophie confirms the order, +1:30 hours for ATM, +1 hour for any other types of payment.)</font></li>

            <li>Jika member tidak complain dalam waktu 24 jam setelah pesanan divalidasi oleh BC dan tidak ada konfirmasi pembayaran diterima di Sophie HQ maka orderan dianggap batal
            <font class="italicpink">(If the member doesn't complain 24 hours after the order confirmation by the BC, it will be considered as cancelled if the HQ hasn't received any payment confirmation.)</font></li>

            <li>Jika ada masalah pembayaran, member harus memberikan bukti pembayaran yang sudah berhasil untuk penggantian atau pengembalian uang
            <font class="italicpink">(If there is any problem related to the payment, the member must give a proof of payment for a refund.)</font></li>

            <li>Pembayaran akan direconsil oleh HQ Sophie selama jam kerja Kasir. Order dianggap final setelah validasi pembayaran oleh Kasir Sophie HQ. 
            <font class="italicpink">(The HQ will reconcile payment during the working hours of the Cashier. Sophie Paris Cashier considers order as accepted and final only after validation of the payment.)</font></li>
        </ol>
	
	<?if($ctrl->firstlogin) { ?>
		<input class="inputcheck" type="checkbox" name="agree" id="agree" value="1" />
                <label for="agree">Saya setuju dengan persyaratan diatas.</label>
		<? if($ctrl->varvalue("errmsg") != '') { ?><br><span class="errmsg"><?=$ctrl->varvalue("errmsg")?></span><?}?>
		<br><input type="button" class="buttongo" onclick="setaction('setuju');"  value="Setuju"/>
	<? } else { ?>
		<input type="submit" class="buttongo" onclick="setaction('lanjut');" value="Lanjut &gt;&gt" />
	<? } ?>
	
	
<?include "mbrfooter.php";?>