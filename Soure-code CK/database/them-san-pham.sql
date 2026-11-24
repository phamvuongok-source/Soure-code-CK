-- ============================================================================
-- File thêm dữ liệu sản phẩm mẫu phong phú cho CSDL `new1`
-- Đảm bảo mỗi danh mục có ít nhất 10-12 sản phẩm
-- ============================================================================

INSERT INTO `san_pham` (`id_danh_muc`, `ten_san_pham`, `thuong_hieu`, `gia_ban`, `so_luong_ton`, `mo_ta`, `hinh_anh`) VALUES
-- 1. Đàn Guitar
(1, 'Đàn Guitar Classic Cordoba C1M', 'Cordoba', 4200000.00, 20, 'Đàn guitar classic dây nylon chuẩn truyền thống Tây Ban Nha, mặt top gỗ Spruce, âm trầm ấm và ngân vang, cần đàn dễ bấm cho người mới.', NULL),
(1, 'Đàn Guitar Acoustic Taylor Academy 10', 'Taylor', 16500000.00, 8, 'Thiết kế dáng Dreadnought mạnh mẽ tích hợp vát tỳ tay Ergonomic Armrest giúp chơi đàn thoải mái hàng giờ liền. Âm thanh Taylor đỉnh cao.', NULL),
(1, 'Đàn Guitar Điện Ibanez GRG170DX', 'Ibanez', 6800000.00, 15, 'Dòng RG huyền thoại của Ibanez với cần đàn mỏng tốc độ cao, cấu hình 3 pickup HSH Infinity R cho chất âm Rock/Metal uy lực.', NULL),
(1, 'Đàn Guitar Classic Yamaha C40', 'Yamaha', 2750000.00, 30, 'Cây đàn guitar cổ điển bán chạy số 1 tại các trường nhạc. Độ hoàn thiện tỉ mỉ từ gỗ Meranti và Spruce, phím bấm êm tay.', NULL),
(1, 'Đàn Guitar Acoustic Fender CD-60S', 'Fender', 5600000.00, 14, 'Mặt top gỗ Solid Spruce nguyên tấm, lưng hông gỗ Mahogany mang lại âm mộc tự nhiên vang rền, cạnh phím được bo tròn êm ái.', NULL),
(1, 'Đàn Guitar Điện Epiphone Les Paul Standard 60s', 'Epiphone', 15900000.00, 6, 'Tái hiện mẫu đàn huyền thoại thập niên 60 với thân gỗ Mahogany đặc ruột, mặt maple vân lửa và bộ đôi pickup ProBucker ấm áp.', NULL),
(1, 'Đàn Guitar Bass Yamaha TRBX174', 'Yamaha', 5400000.00, 12, 'Cây bass 4 dây linh hoạt với cấu hình pickup kết hợp P/J, thân đàn gỗ Alder nhẹ và cân đối, phù hợp biểu diễn mọi thể loại.', NULL),
(1, 'Đàn Guitar Acoustic Martin DX1E Mahogany', 'Martin', 22000000.00, 5, 'Cây đàn cao cấp từ hãng guitar lừng danh Martin & Co của Mỹ, tích hợp hệ thống pickup Fishman MX cho âm thanh sân khấu hoàn hảo.', NULL),
(1, 'Đàn Guitar Silent Yamaha SLG200S', 'Yamaha', 17200000.00, 7, 'Thiết kế khung rời siêu di động, công nghệ mô phỏng âm thanh SRT độc quyền của Yamaha, tập luyện ban đêm qua tai nghe cực êm.', NULL),

-- 2. Đàn Piano & Keyboard
(2, 'Đàn Piano Điện Yamaha P-45B', 'Yamaha', 11200000.00, 18, 'Bàn phím Graded Hammer Standard (GHS) 88 phím cho cảm giác nặng nhẹ chân thực như đại dương cầm, kích thước gọn nhẹ dễ di chuyển.', NULL),
(2, 'Đàn Piano Điện Casio Privia PX-S1100', 'Casio', 16800000.00, 10, 'Cây piano điện mỏng nhất thế giới với thiết kế mặt gương cảm ứng sang trọng, kết nối không dây Bluetooth Audio/MIDI.', NULL),
(2, 'Đàn Piano Cơ Upright Yamaha U3H Nhật Bản', 'Yamaha', 68000000.00, 3, 'Mẫu đàn upright piano cơ cổ điển nổi tiếng nhất của Yamaha, chiều cao 131cm cho bảng cộng hưởng lớn, chất âm dày dặn chuẩn phòng thu.', NULL),
(2, 'Đàn Piano Điện Kawai KDP-120', 'Kawai', 23500000.00, 9, 'Hệ thống phím Responsive Hammer Compact II cùng âm sắc mẫu từ cây đàn Grand Shigeru Kawai SK-EX danh tiếng toàn cầu.', NULL),
(2, 'Đàn Organ Keyboard Yamaha PSR-SX600', 'Yamaha', 18900000.00, 11, 'Nhạc cụ keyboard chuyên nghiệp phục vụ đi show với 850 tiếng nhạc cụ, 415 điệu đệm tự động và tính năng Unison & Accent.', NULL),
(2, 'Đàn Piano Điện Yamaha Clavinova CLP-735', 'Yamaha', 38500000.00, 4, 'Dòng Clavinova cao cấp với bàn phím GrandTouch-S bằng gỗ, lấy mẫu âm thanh từ 2 đại dương cầm Yamaha CFX và Bösendorfer Imperial.', NULL),
(2, 'Đàn Keyboard Casio Casiotone CT-S300', 'Casio', 3850000.00, 25, 'Đàn organ di động có tay cầm tiện lợi, 61 phím phản hồi lực chạm (touch response), bánh xe Pitch Bend và hỗ trợ kết nối máy tính.', NULL),
(2, 'Đàn Piano Điện Roland RP-107', 'Roland', 22900000.00, 7, 'Thiết kế tủ gỗ truyền thống sang trọng, bàn phím PHA-4 giả ngà voi chống trượt, nguồn âm thanh SuperNATURAL biểu cảm cao.', NULL),
(2, 'Đàn Synthesizer Korg Kross 2 61 Phím', 'Korg', 17500000.00, 5, 'Máy trạm âm nhạc workstation đa năng nhẹ chỉ 3.8kg, tích hợp bộ tạo mẫu sampler 16 pad và hơn 1000 âm sắc phong phú cho biểu diễn.', NULL),
(2, 'Đàn Piano Điện Casio CDP-S110', 'Casio', 9800000.00, 16, 'Piano điện tử cơ bản 88 phím nặng chuẩn mực với giá thành hợp lý, có thể dùng nguồn pin AA để luyện tập ngoài trời.', NULL),
(2, 'Đàn Piano Cơ Kawai BL-71 Special', 'Kawai', 75000000.00, 2, 'Đàn piano cơ upright cao cấp với mặt giá nhạc lớn phong cách đại dương cầm, phím đàn đầm tay và âm bass rền vang mạnh mẽ.', NULL),

-- 3. Trống & Bộ gõ
(3, 'Bộ Trống Điện Tử Roland TD-07KV', 'Roland', 25500000.00, 7, 'Trang bị toàn bộ mặt lưới Mesh-head 2 lớp siêu êm ái của Roland, mô đun âm thanh TD-07 chân thực và kết nối Bluetooth không dây.', NULL),
(3, 'Bộ Trống Điện Tử Yamaha DTX402K', 'Yamaha', 10900000.00, 12, 'Giải pháp luyện tập tại nhà không gây tiếng ồn, tích hợp 10 chế độ đào tạo nhịp điệu chuyên nghiệp và ứng dụng di động DTX402 Touch.', NULL),
(3, 'Bộ Trống Jazz Tama Imperialstar IP52H6W', 'Tama', 18200000.00, 5, 'Bộ trống cơ 5 món danh tiếng của Tama, vỏ gỗ Poplar 6 lớp dày 8mm kèm bộ chân giá đôi Stage Master vững chãi và cùm treo tom linh hoạt.', NULL),
(3, 'Trống Cajon Nissi Standard CS01', 'Nissi', 1250000.00, 22, 'Trống cajon gỗ thông tự nhiên, mặt gõ gỗ Birch cao cấp cho tiếng bass sâu và tiếng snare đanh giòn, thích hợp chơi acoustic đường phố.', NULL),
(3, 'Bộ Trống Điện Tử Alesis Nitro Max Kit', 'Alesis', 12800000.00, 9, 'Thế hệ trống lưới Mesh mới nhất với snare 10 inch 2 vùng đánh, mô đun chứa thư viện âm thanh BFD huyền thoại và giá đỡ thép cứng cáp.', NULL),
(3, 'Trống Djembe Remo Apex 12 inch', 'Remo', 5600000.00, 8, 'Trống châu Phi djembe hiện đại của Remo USA với mặt trống Skyndeep chống biến dạng thời tiết, âm bass bùng nổ và âm slap chói lọi.', NULL),
(3, 'Bộ Lá Cymbal Zildjian Planet Z PLZ4pk', 'Zildjian', 6200000.00, 10, 'Bộ chũm chọe cymbal chất lượng cao gồm 14 inch Hi-hat, 16 inch Crash và 20 inch Ride sản xuất tại Mỹ từ hợp kim đồng thau nguyên chất.', NULL),
(3, 'Bộ Trống Cơ Ludwig Accent Drive LC175', 'Ludwig', 13900000.00, 4, 'Thương hiệu trống gắn liền với tay trống Ringo Starr của The Beatles, cấu hình 5 trống hoàn chỉnh kèm cymbal và ghế ngồi.', NULL),
(3, 'Trống Lục Lạc Tambourine Meinl TMT1BK', 'Meinl', 650000.00, 35, 'Khung nhựa ABS hình bán nguyệt công thái học, hàng chuông thép kép không gỉ cho âm thanh sắc sảo, thích hợp hát phụ họa và ban nhạc.', NULL),
(3, 'Bộ Trống Bongo Meinl Headliner HB100', 'Meinl', 3100000.00, 10, 'Cặp trống bongo 6 3/4 inch và 8 inch gỗ cây cao su thân thiện môi trường, mặt da trâu chọn lọc cho tiếng gõ ấm và vang.', NULL),
(3, 'Trống Lưỡi Thép Steel Tongue Drum Hluru 12 Inch 13 Nốt', 'Hluru', 2400000.00, 15, 'Nhạc cụ gõ trị liệu âm thanh thiền định, chế tác từ hợp kim titan thép phủ sơn tĩnh điện nano chống gỉ sét, âm ngân dài du dương.', NULL),

-- 4. Kèn & Nhạc cụ hơi
(4, 'Kèn Saxophone Alto Yamaha YAS-280', 'Yamaha', 26500000.00, 6, 'Mẫu kèn Alto Saxophone tiêu chuẩn vàng dành cho người học và biểu diễn bán chuyên, khớp nối cổ kèn cải tiến cho âm chuẩn xác.', NULL),
(4, 'Kèn Trumpet Yamaha YTR-2330', 'Yamaha', 13200000.00, 8, 'Kèn trumpet Bb thiết kế công thái học nhẹ nhàng cho người mới, piston hợp kim Monel bền bỉ và chuông kèn 2 mảnh bằng đồng thau vàng.', NULL),
(4, 'Sáo Flute Yamaha YFL-222', 'Yamaha', 12800000.00, 10, 'Thân sáo và phím bằng hợp kim Niken mạ bạc bóng bẩy, thiết kế phím đóng (Closed Keys) với phím trỏ nhô (Pointed Key Arms) cao cấp.', NULL),
(4, 'Kèn Saxophone Tenor Yamaha YTS-280', 'Yamaha', 34000000.00, 4, 'Dòng Tenor Saxophone cho dải âm trầm ấm nồng nàn đặc trưng nhạc Jazz cổ điển, phím bấm nhẹ êm và độ nhạy phím vượt trội.', NULL),
(4, 'Kèn Harmonica Hohner Marine Band 1896 Classic', 'Hohner', 980000.00, 30, 'Cây kèn harmonica diatonic 10 lỗ huyền thoại của nước Đức từ năm 1896, thân gỗ lê truyền thống tạo nên âm sắc Blues đích thực.', NULL),
(4, 'Kèn Saxophone Soprano Victoria SAS-600', 'Victoria', 9500000.00, 7, 'Thiết kế dáng thẳng sang trọng mạ vàng kim cương, âm thanh thanh thoát bay bổng ở dải cao, tặng kèm 2 cổ kèn thẳng và cong.', NULL),
(4, 'Kèn Trombone Yamaha YSL-354', 'Yamaha', 17500000.00, 5, 'Cây kèn Tenor Trombone tiêu chuẩn với cần trượt mạ crom siêu trơn tru, dải âm trầm uy nghi mạnh mẽ cho dàn nhạc giao hưởng.', NULL),
(4, 'Kèn Clarinet Yamaha YCL-255', 'Yamaha', 15300000.00, 8, 'Thân kèn bằng nhựa ABS mờ giả gỗ Grenadilla chống nứt nẻ thời tiết, phím mạ bạc bền đẹp và chỗ tỳ ngón cái có thể tinh chỉnh.', NULL),
(4, 'Kèn Harmonica Suzuki Promaster MR-350', 'Suzuki', 1350000.00, 18, 'Harmonica cao cấp chế tạo chính xác tại Nhật Bản với thân nhôm đúc nguyên khối, nắp kèn inox bóng loáng và lưỡi gà đồng phosphor.', NULL),
(4, 'Sáo Recorder Yamaha YRS-24B Hệ Baroque', 'Yamaha', 120000.00, 50, 'Sáo dọc soprano giọng C phổ biến nhất trong chương trình giáo dục âm nhạc học đường, làm từ nhựa ABS an toàn không độc hại.', NULL),
(4, 'Kèn Melodica Phím Thổi Yamaha Pianica P-37D', 'Yamaha', 1450000.00, 20, 'Kèn phím 37 phím âm thanh to rõ và trong sáng, thân màu nâu đỏ sang trọng kèm ống thổi mềm và hộp cứng chống sốc.', NULL),
(4, 'Kèn Saxophone Alto Selmer Paris Series III', 'Selmer', 85000000.00, 2, 'Kiệt tác Saxophone chế tác thủ công tại Pháp, cây kèn mơ ước của mọi nghệ sĩ saxophone thính phòng và biểu diễn quốc tế.', NULL),

-- 5. Phụ kiện âm nhạc
(5, 'Amplifier Guitar Boss Katana-50 MkII', 'Boss', 7800000.00, 8, 'Amply guitar điện 50W bán chạy nhất với 5 đặc tính khuếch đại độc đáo, hơn 60 hiệu ứng Boss chất lượng phòng thu và loa 12 inch.', NULL),
(5, 'Dây Đàn Guitar Acoustic D\'Addario EJ13 80/20 Bronze', 'D\'Addario', 170000.00, 60, 'Bộ dây guitar acoustic sản xuất tại Mỹ cỡ 11-52 Custom Light, chất liệu đồng 80/20 cho dải âm tươi sáng, bấm êm tay.', NULL),
(5, 'Máy Lên Dây Tuner Đa Năng Korg CA-2', 'Korg', 380000.00, 40, 'Máy chỉnh âm Chromatic độ chính xác cao từ Korg Nhật Bản, màn hình hiển thị kim đo rõ nét, dùng cho guitar, violin, kèn hơi.', NULL),
(5, 'Capo Đàn Guitar Acoustic & Classic Alice A007G', 'Alice', 90000.00, 50, 'Kẹp tăng tông hợp kim nhôm đúc phủ sơn tĩnh điện ánh vàng kim, đệm cao su silicon chống trầy xước cần đàn và giữ nốt chuẩn.', NULL),
(5, 'Chân Để Đàn Guitar Chữ A Gấp Gọn Soundking', 'Soundking', 250000.00, 30, 'Giá đỡ đàn guitar cơ và điện dạng chữ A vững chắc, bọc đệm xốp EVA chống trơn trượt và có thể gấp gọn bỏ vào ba lô tiện lợi.', NULL),
(5, 'Tai Nghe Kiểm Âm Audio-Technica ATH-M20x', 'Audio-Technica', 1450000.00, 15, 'Tai nghe kiểm âm phòng thu chuyên nghiệp, củ loa 40mm với nam châm đất hiếm, dải tần đáp ứng rộng cho âm bass trung thực.', NULL),
(5, 'Bộ Dây Đàn Guitar Điện Ernie Ball Regular Slinky 2221', 'Ernie Ball', 190000.00, 45, 'Bộ dây đàn guitar điện cỡ 10-46 huyền thoại được tin dùng bởi Slash, Eric Clapton và Jimmy Page, chất âm cân bằng ấm áp.', NULL),
(5, 'Bàn Đạp Phơ Boss DS-1 Distortion', 'Boss', 1550000.00, 10, 'Cục phơ méo tiếng màu cam kinh điển của Boss từ năm 1978, cho chất tiếng overdrive/distortion gai góc đặc trưng của hard rock.', NULL),
(5, 'Bao Da Đàn Guitar 5 Lớp Chống Thấm Nước Re:quiem', 'Re:quiem', 450000.00, 25, 'Bao đựng guitar mút xốp dày 20mm chống va đập tuyệt hảo, vải dù Oxford 600D trượt nước, quai đeo lưng đệm mút chịu lực cao.', NULL),
(5, 'Chân Micro Đứng Sân Khấu Kèm Kẹp Soundking', 'Soundking', 390000.00, 20, 'Chân micro 3 chạc kim loại cứng cáp sơn chống gỉ, khớp xoay kim loại linh hoạt điều chỉnh độ cao từ 90cm đến 1m60.', NULL),
(5, 'Bộ Vệ Sinh & Dưỡng Đàn Dunlop System 65 Maintenance Kit', 'Dunlop', 420000.00, 18, 'Combo bảo dưỡng gồm dung dịch lau sạch bóng thân đàn Dunlop 654 và dầu dưỡng mặt phím gỗ Fretboard 65 Ultimate Lemon Oil.', NULL);
