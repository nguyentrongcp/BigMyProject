<html>
<head>
    <title>TẠO MÃ IN QRCODE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.3.0/paper.css">
    <link rel="stylesheet" href="/giaodien/dist/css/bootstrap.min.css">
    <!-- jQuery -->
    <script src="/giaodien/plugins/jquery/jquery.min.js"></script>
    <script src="/giaodien/dist/js/qrcode.js"></script>
    <script src="/giaodien/my_plugins/print/jquery-print.js"></script>
    <script src="/giaodien/my_plugins/numeral-js/numeral.min.js"></script>
    <style>
        section {
            margin-right: auto !important;
            margin-left: auto !important;
            padding-top: 15px;
        }
        .col-qrcode {
            display: flex;
            margin: 0 auto;
            width: 124mm;
        }
        .box-qrcode {
            width: 60mm;
            height: 40mm;
            text-align: center;
            border: 1px solid #000000;
            padding-left: 3mm;
            position: relative;
        }
        body {
            font-family: 'Open Sans', sans-serif !important;
        }
        .qrcode > canvas {
            height: 100% !important;
        }
        .truncate-twolines {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        @media print {
            canvas {
                max-height: none !important;
                height: 100% !important;
            }
        }
    </style>
</head>
<body class="A5">
<!--    <section class="sheet">-->
<!--        <article id="qrcode-container">-->
<!--            <div class="col-qrcode">-->
<!--                <div class="box-qrcode">-->
<!--                    <div style="margin-top: 7mm; font-weight: bolder; height: 10mm; padding-right: 5mm">DIỆT SÂU GỐI LỨA (NHỰA) 100EC 200ML</div>-->
<!--                    <div style="display: flex; height: 23mm; padding-top: 2mm;-->
<!--                    padding-bottom: 5mm; position: absolute; width: calc(100% - 5mm); bottom: 0">-->
<!--                        <div class="qrcode">-->
<!---->
<!--                        </div>-->
<!--                        <div style="font-weight: 800; width: 100%">-->
<!--                            <div class="text-right" style="height: 10mm; position: relative">-->
<!--                                <div style="padding-right: 5mm; position: absolute; width: 100%; bottom: 0; margin-bottom: 5px">170.000VNĐ</div>-->
<!--                                <div style="border-bottom: 1px solid #000000; position: absolute; width: calc(100% - 3px); bottom: 0; right: 0"></div>-->
<!--                            </div>-->
<!--                            <div class="text-right" style="padding-right: 5mm">20đ</div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </article>-->
<!--    </section>-->
</body>
<footer>
    <script>
        let danhsachs = JSON.parse('{!! $danhsachs !!}')
        let chinhanh_id = '{{ $chinhanh_id }}'

        $(function () {
            let col_qrcode = '';
            let section = '';
            let page = 0;
            $.each(danhsachs, function (key, value) {
                let dongia = numeral(value.dongia).format('0,0');
                for(let i=1; i<=value.soluong; i++) {
                    page++;
                    if (page % 10 === 1) {
                        section = '';
                        section = $('<section class="sheet"><article></article></section>');
                        $('body').append(section);
                    }
                    if ($(col_qrcode).find('.box-qrcode').length === 2 || col_qrcode === '') {
                        col_qrcode = '';
                        col_qrcode = $('<div class="col-qrcode"></div>');
                        $(section).find('article').append(col_qrcode);
                    }
                    let temp = $('<div style="display: flex; justify-content: center; width: 50%"><div class="box-qrcode">\n' +
                        '                    <div class="d-flex justify-content-center align-items-center" style="margin-top: 3mm; height: 15mm; padding-right: 3mm"><div class="truncate-twolines" style=" font-size: 15px; line-height: 19px; font-weight: bolder">'+ value.ten +'</div></div>\n' +
                        '                    <div style="display: flex; height: 23mm; padding-top: 2mm;\n' +
                        '                    padding-bottom: 3mm; position: absolute; width: calc(100% - 6mm); bottom: 0">\n' +
                        '                        <div class="qrcode">\n' +
                        '\n' +
                        '                        </div>\n' +
                        '                        <div style="width: 100%" class="ps-2 d-flex flex-column justify-content-center">\n' +
                        '                            <div style="position: relative">\n' +
                        '<div class="d-flex w-100 justify-content-center"><span class="fw-bolder">' + dongia + ' VNĐ</span></div>' +
                        '                                <div style="border-bottom: 1px solid #9b9b9b; position: absolute; width: 100%; bottom: 0; right: 0"></div>\n' +
                        '                            </div>\n' +
                        '                            <div class="d-flex justify-content-between" style="font-size: 12px">' +
                        '<span>Mã hàng:</span><span class="fw-bolder">' + value.ma + '</span></div>\n' +
                        '                        </div>\n' +
                        '                    </div>\n' +
                        '                </div></div>');
                    tao_qrcode($(temp).find('.qrcode'), 'hanghoa|{"chinhanh_id":"'+chinhanh_id+'","ma":"'+value.ma+'"}');
                    $(col_qrcode).append(temp);
                }
            });

            setTimeout(() => {window.print()}, 100)
        });

        function tao_qrcode(div, ma) {
            $(div).html('').qrcode({
                // render method: 'canvas', 'image' or 'div'
                render: 'canvas',
                // version range somewhere in 1 .. 40
                minVersion: 1,
                maxVersion: 40,
                // error correction level: 'L', 'M', 'Q' or 'H'
                ecLevel: 'L',
                // offset in pixel if drawn onto existing canvas
                left: 0,
                top: 0,
                // size in pixel
                size: 256,
                // code color or image element
                fill: '#000',
                // background color or image element, null for transparent background
                background: null,
                // content
                text: ma,
                // corner radius relative to module width: 0.0 .. 0.5
                radius: 0.5,
                // quiet zone in modules
                quiet: 0,
                // modes
                // 0: normal
                // 1: label strip
                // 2: label box
                // 3: image strip
                // 4: image box
                mode: 0,
                mSize: 0.1,
                mPosX: 0.5,
                mPosY: 0.5,
                fontname: 'sans',
                fontcolor: '#000',
                image: null
            });
        }
    </script>
</footer>
</html>
