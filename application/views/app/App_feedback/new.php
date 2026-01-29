<?php
?>
<html>
<title>Feedback Report</title>

<head>
    <link rel="stylesheet" type="text/css" href="<?php echo SURL ?>assets/css/old_css.css">
</head>

<body style="font-family:Verdana, Arial, Helvetica, sans-serif;">
    <style type="text/css">
        .style1 {
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
        }

        .style7 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: small;
            font-weight: bold;
        }

        .style10 {
            font-size: small;
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
    <style>
        .feedback {
            --normal: #ECEAF3;
            --normal-shadow: #D9D8E3;
            --normal-mouth: #9795A4;
            --normal-eye: #595861;
            --active: #F8DA69;
            --active-shadow: #F4B555;
            --active-mouth: #F05136;
            --active-eye: #313036;
            --active-tear: #76b5e7;
            --active-shadow-angry: #e94f1d;
            margin: 0;
            padding: 0;
            list-style: none;
            display: flex;

            li {
                position: relative;
                border-radius: 50%;
                background: var(--sb, var(--normal));
                box-shadow: inset 3px -3px 4px var(--sh, var(--normal-shadow));
                transition: background .4s, box-shadow .4s, transform .3s;
                -webkit-tap-highlight-color: transparent;

                &:not(:last-child) {
                    margin-right: 20px;
                }

                div {
                    width: 40px;
                    height: 40px;
                    position: relative;
                    transform: perspective(240px) translateZ(4px);

                    svg,
                    &:before,
                    &:after {
                        display: block;
                        position: absolute;
                        left: var(--l, 9px);
                        top: var(--t, 13px);
                        width: var(--w, 8px);
                        height: var(--h, 2px);
                        transform: rotate(var(--r, 0deg)) scale(var(--sc, 1)) translateZ(0);
                    }

                    svg {
                        fill: none;
                        stroke: var(--s);
                        stroke-width: 2px;
                        stroke-linecap: round;
                        stroke-linejoin: round;
                        transition: stroke .4s;

                        &.eye {
                            --s: var(--e, var(--normal-eye));
                            --t: 17px;
                            --w: 7px;
                            --h: 4px;

                            &.right {
                                --l: 23px;
                            }
                        }

                        &.mouth {
                            --s: var(--m, var(--normal-mouth));
                            --l: 11px;
                            --t: 23px;
                            --w: 18px;
                            --h: 7px;
                        }
                    }

                    &:before,
                    &:after {
                        content: '';
                        z-index: var(--zi, 1);
                        border-radius: var(--br, 1px);
                        background: var(--b, var(--e, var(--normal-eye)));
                        transition: background .4s;
                    }
                }

                &.angry {
                    --step-1-rx: -24deg;
                    --step-1-ry: 20deg;
                    --step-2-rx: -24deg;
                    --step-2-ry: -20deg;

                    div {
                        &:before {
                            --r: 20deg;
                        }

                        &:after {
                            --l: 23px;
                            --r: -20deg;
                        }

                        svg {
                            &.eye {
                                stroke-dasharray: 4.55;
                                stroke-dashoffset: 8.15;
                            }
                        }
                    }

                    &.active {
                        animation: angry 1s linear;

                        div {
                            &:before {
                                --middle-y: -2px;
                                --middle-r: 22deg;
                                animation: toggle .8s linear forwards;
                            }

                            &:after {
                                --middle-y: 1px;
                                --middle-r: -18deg;
                                animation: toggle .8s linear forwards;
                            }
                        }
                    }
                }

                &.sad {
                    --step-1-rx: 20deg;
                    --step-1-ry: -12deg;
                    --step-2-rx: -18deg;
                    --step-2-ry: 14deg;

                    div {

                        &:before,
                        &:after {
                            --b: var(--active-tear);
                            --sc: 0;
                            --w: 5px;
                            --h: 5px;
                            --t: 15px;
                            --br: 50%;
                        }

                        &:after {
                            --l: 25px;
                        }

                        svg {
                            &.eye {
                                --t: 16px;
                            }

                            &.mouth {
                                --t: 24px;
                                stroke-dasharray: 9.5;
                                stroke-dashoffset: 33.25;
                            }
                        }
                    }

                    &.active {
                        div {

                            &:before,
                            &:after {
                                animation: tear .6s linear forwards;
                            }
                        }
                    }
                }

                &.ok {
                    --step-1-rx: 4deg;
                    --step-1-ry: -22deg;
                    --step-1-rz: 6deg;
                    --step-2-rx: 4deg;
                    --step-2-ry: 22deg;
                    --step-2-rz: -6deg;

                    div {
                        &:before {
                            --l: 12px;
                            --t: 17px;
                            --h: 4px;
                            --w: 4px;
                            --br: 50%;
                            box-shadow: 12px 0 0 var(--e, var(--normal-eye));
                        }

                        &:after {
                            --l: 13px;
                            --t: 26px;
                            --w: 14px;
                            --h: 2px;
                            --br: 1px;
                            --b: var(--m, var(--normal-mouth));
                        }
                    }

                    &.active {
                        div {
                            &:before {
                                --middle-s-y: .35;
                                animation: toggle .2s linear forwards;
                            }

                            &:after {
                                --middle-s-x: .5;
                                animation: toggle .7s linear forwards;
                            }
                        }
                    }
                }

                &.good {
                    --step-1-rx: -14deg;
                    --step-1-rz: 10deg;
                    --step-2-rx: 10deg;
                    --step-2-rz: -8deg;

                    div {
                        &:before {
                            --b: var(--m, var(--normal-mouth));
                            --w: 5px;
                            --h: 5px;
                            --br: 50%;
                            --t: 22px;
                            --zi: 0;
                            opacity: .5;
                            box-shadow: 16px 0 0 var(--b);
                            filter: blur(2px);
                        }

                        &:after {
                            --sc: 0;
                        }

                        svg {
                            &.eye {
                                --t: 15px;
                                --sc: -1;
                                stroke-dasharray: 4.55;
                                stroke-dashoffset: 8.15;
                            }

                            &.mouth {
                                --t: 22px;
                                --sc: -1;
                                stroke-dasharray: 13.3;
                                stroke-dashoffset: 23.75;
                            }
                        }
                    }

                    &.active {
                        div {
                            svg {
                                &.mouth {
                                    --middle-y: 1px;
                                    --middle-s: -1;
                                    animation: toggle .8s linear forwards;
                                }
                            }
                        }
                    }
                }

                &.happy {
                    div {
                        --step-1-rx: 18deg;
                        --step-1-ry: 24deg;
                        --step-2-rx: 18deg;
                        --step-2-ry: -24deg;

                        &:before {
                            --sc: 0;
                        }

                        &:after {
                            --b: var(--m, var(--normal-mouth));
                            --l: 11px;
                            --t: 23px;
                            --w: 18px;
                            --h: 8px;
                            --br: 0 0 8px 8px;
                        }

                        svg {
                            &.eye {
                                --t: 14px;
                                --sc: -1;
                            }
                        }
                    }

                    &.active {
                        div {
                            &:after {
                                --middle-s-x: .95;
                                --middle-s-y: .75;
                                animation: toggle .8s linear forwards;
                            }
                        }
                    }
                }

                &:not(.active) {
                    cursor: pointer;

                    &:active {
                        transform: scale(.925);
                    }
                }

                &.active {
                    --sb: var(--active);
                    --sh: var(--active-shadow);
                    --m: var(--active-mouth);
                    --e: var(--active-eye);

                    div {
                        animation: shake .8s linear forwards;
                    }
                }
            }
        }

        @keyframes shake {
            30% {
                transform: perspective(240px) rotateX(var(--step-1-rx, 0deg)) rotateY(var(--step-1-ry, 0deg)) rotateZ(var(--step-1-rz, 0deg)) translateZ(10px);
            }

            60% {
                transform: perspective(240px) rotateX(var(--step-2-rx, 0deg)) rotateY(var(--step-2-ry, 0deg)) rotateZ(var(--step-2-rz, 0deg)) translateZ(10px);
            }

            100% {
                transform: perspective(240px) translateZ(4px);
            }
        }

        @keyframes tear {
            0% {
                opacity: 0;
                transform: translateY(-2px) scale(0) translateZ(0);
            }

            50% {
                transform: translateY(12px) scale(.6, 1.2) translateZ(0);
            }

            20%,
            80% {
                opacity: 1;
            }

            100% {
                opacity: 0;
                transform: translateY(24px) translateX(4px) rotateZ(-30deg) scale(.7, 1.1) translateZ(0);
            }
        }

        @keyframes toggle {
            50% {
                transform: translateY(var(--middle-y, 0)) scale(var(--middle-s-x, var(--middle-s, 1)), var(--middle-s-y, var(--middle-s, 1))) rotate(var(--middle-r, 0deg));
            }
        }

        @keyframes angry {
            40% {
                background: var(--active);
            }

            45% {
                box-shadow: inset 3px -3px 4px var(--active-shadow), inset 0 8px 10px var(--active-shadow-angry);
            }
        }

        html {
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
        }

        * {
            box-sizing: inherit;

            &:before,
            &:after {
                box-sizing: inherit;
            }
        }

        // Center & dribbble
        body {
            min-height: 100vh;
            display: flex;
            font-family: 'Roboto', Arial;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            background: #F9F9FC;

            .dribbble {
                position: fixed;
                display: block;
                right: 20px;
                bottom: 20px;

                img {
                    display: block;
                    height: 28px;
                }
            }

            .twitter {
                position: fixed;
                display: block;
                right: 64px;
                bottom: 14px;

                svg {
                    width: 32px;
                    height: 32px;
                    fill: #1da1f2;
                }
            }
        }
    </style>
    <table width="80%" align="center" border="0" class="imagetable">
        <?php
        $hosp_id = '';
        $hosp_name = '';
        $hosp_address_1 = '';
        $hosp_address_2 = '';
        $hosp_nums = '';
        $hosp_fax = '';
        $hosp_email = '';
        $hosp_url = '';
        $hosp_img = '';
        $qresult = $this->db->query("select * from tbl_company where id=1")->row_array();
        $hosp_id = $qresult['id'];
        $hosp_name = $qresult['business_name'];
        $hosp_address_1 = $qresult['address'];
        $hosp_nums = $qresult['phone'];
        $hosp_email = $qresult['email'];
        $hosp_img = $qresult['logo'];
        ?>
        <tr align="center">
            <td width="30%"><img src="<?php echo IMG . "company/" . $hosp_img ?>" width="240" height="97"> </td>
            <td colspan="2" width="40%"> <span align="center" style="color :#153860; font-family: times New Roman;   font-size: 18px;  height: 29px;">Feedback Report<?php $csv_outputs .= ",,," . "Purchase Report" . "\n"; ?><?php $csv_output .= "\t\t\t" . "Purchase Report" . "\n"; ?></span>
                <br>
                <span align="center" style="color :#153860; font-family: times New Roman;   font-size: 13px;  height: 29px;">
                    <?php print $fromdate . ' To ' . $todate; ?><?php $csv_outputs .= ",,," . $fromdate . ' To ' . $todate . "\n"; ?><?php $csv_output .= "\t\t\t" . $fromdate . ' To ' . $todate . "\n"; ?></span>
                <br>
            </td>
            <td width="30%" style="text-align:right" valign="bottom">
                <span style="font-size:12px; color:#1f2153; font-weight:900;"><i class="fa fa-h-square fa-fw"></i><?php print $hosp_name;
                                                                                                                    ?> </span><br>
                <span style="font-size:12px; color:#1f2153; font-weight:900;"><i class="fa fa-h-square fa-fw"></i><?php print $hosp_address_1;
                                                                                                                    if ($hosp_address_2) {
                                                                                                                        print '<br>' . $hosp_address_2;
                                                                                                                    }
                                                                                                                    ?> </span><br>
                <?php if ($hosp_nums) { ?>
                    <span style="font-size:11px; color:#1f2153; font-weight:400;"><i class="fa fa-phone fa-fw"></i><?php print $hosp_nums; ?></span> <br>
                <?php } ?>
                <?php if ($hosp_fax) { ?>
                    <span style="font-size:11px; color:#1f2153; font-weight:400;"><i class="fa fa-fax fa-fw"></i><?php print $hosp_fax; ?></span> <br>
                <?php } ?>
                <?php if ($hosp_email) { ?>
                    <span style="font-size:11px; color:#1f2153; font-weight:400;"><i class="fa fa-envelope-o fa-fw"></i><?php print $hosp_email; ?></span> <br>
                <?php } ?>
                <?php if ($hosp_url) { ?>
                    <span style="font-size:11px; color:#1f2153; font-weight:400;"><i class="fa fa-globe fa-fw"></i><?php print $hosp_url; ?></span> <br>
                <?php } ?>
            </td>
        </tr>
        <?php
        // $csv_hdr .= ",,Purshase order Report&amp; From Date," . $fromdate . " ,To Date," . $todate . "\n";
        // $csv_hdr .= "Feedback No. ,Supplier Name,Remarks,Book Date,Payment Terms,Partial Delivery\n";
        ?>

    </table>
    <table width="80%" height="30" align="center" class="imagetable">
        <tr class="exist_rec_sb_high_main">
            <td>
                <div align="left">SR #<?php $csv_outputs .= 'SR #' . ","; ?><?php $csv_output .= 'SR #' . "\t"; ?></div>
            </td>
            <td>
                <div align="left">User Name<?php $csv_outputs .= 'User Name' . ","; ?><?php $csv_output .= 'User Name' . "\t"; ?></div>
            </td>
            <td>
                <div align="left">Date<?php $csv_outputs .= 'Date' . ","; ?><?php $csv_output .= 'Date' . "\t"; ?></div>
            </td>
            <td>
                <div align="left">Rating<?php $csv_outputs .= 'Rating' . ","; ?><?php $csv_output .= 'Rating' . "\t"; ?></div>
            </td>
            <td>
                <div align="left">Feedback<?php $csv_outputs .= 'Feedback' . ","; ?><?php $csv_output .= 'Feedback' . "\t"; ?></div>
            </td>
        </tr>
        <?php
        $count = 0;
        foreach ($report as $key => $row) {
            $userid = $row['userid'];
            $count++;
        ?>
            <tr class="even_frm_top" id="row">
                <td>
                    <?php echo $count; ?> <?php $csv_outputs .= $count . ","; ?> <?php $csv_output .= $count . "\t"; ?>
                </td>
                <td>
                    <?php echo ucwords($row['name']); ?> <?php $csv_outputs .= $row['name'] . ","; ?> <?php $csv_output .= $row['name'] . "\t"; ?>
                </td>
                <td>
                    <?php echo $row['created_date']; ?> <?php $csv_outputs .= $row['created_date'] . ","; ?> <?php $csv_output .= $row['created_date'] . "\t"; ?>
                </td>
                <td>
                    <ul class="feedback">

                        <li class="happy <?php if ($row['rating'] == '5') { ?> active <?php } ?>">
                            <div>
                                <svg class="eye left">
                                    <use xlink:href="#eye">
                                </svg>
                                <svg class="eye right">
                                    <use xlink:href="#eye">
                                </svg>
                            </div>
                        </li>
                        <li class="good <?php if ($row['rating'] == '4') { ?> active <?php } ?>">
                            <div>
                                <svg class="eye left">
                                    <use xlink:href="#eye">
                                </svg>
                                <svg class="eye right">
                                    <use xlink:href="#eye">
                                </svg>
                                <svg class="mouth">
                                    <use xlink:href="#mouth">
                                </svg>
                            </div>
                        </li>
                        <li class="ok <?php if ($row['rating'] == '3') { ?> active <?php } ?>">
                            <div></div>
                        </li>
                        <li class="sad <?php if ($row['rating'] == '2') { ?> active <?php } ?>">
                            <div>
                                <svg class="eye left">
                                    <use xlink:href="#eye">
                                </svg>
                                <svg class="eye right">
                                    <use xlink:href="#eye">
                                </svg>
                                <svg class="mouth">
                                    <use xlink:href="#mouth">
                                </svg>
                            </div>
                        </li>
                        <li class="angry <?php if ($row['rating'] == '1') { ?> active <?php } ?>">
                            <div>
                                <svg class="eye left">
                                    <use xlink:href="#eye">
                                </svg>
                                <svg class="eye right">
                                    <use xlink:href="#eye">
                                </svg>
                                <svg class="mouth">
                                    <use xlink:href="#mouth">
                                </svg>
                            </div>
                        </li>
                    </ul>
                    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
                        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 7 4" id="eye">
                            <path d="M1,1 C1.83333333,2.16666667 2.66666667,2.75 3.5,2.75 C4.33333333,2.75 5.16666667,2.16666667 6,1"></path>
                        </symbol>
                        <symbol xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 7" id="mouth">
                            <path d="M1,5.5 C3.66666667,2.5 6.33333333,1 9,1 C11.6666667,1 14.3333333,2.5 17,5.5"></path>
                        </symbol>
                    </svg>

                    <!-- dribbble - twitter -->
                    <a class="dribbble" href="https://dribbble.com/ai" target="_blank"><img src="https://cdn.dribbble.com/assets/dribbble-ball-mark-2bd45f09c2fb58dbbfb44766d5d1d07c5a12972d602ef8b32204d28fa3dda554.svg" alt=""></a>

                </td>
                <td>
                    <?php echo $row['feedback']; ?> <?php $csv_outputs .= $row['feedback'] . ","; ?> <?php $csv_output .= $row['feedback'] . "\t"; ?>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>
    </div>
    <!-- /.main-container -->
    <script type="text/javascript">
        function exportfile() {
            document.export1.submit();
        }
    </script>
    <script>
        function exportfile1() {
            //alert(document.getElementById("csv_output").value);
            document.export2.submit();
        }

        function show_details() {
            document.getElementById("caption").style.display = 'block';
            document.getElementById("details").style.display = 'block';
        }

        function toggle(cls) {
            var cls1 = cls.split("_");
            if (document.getElementById(cls1[0]).style.display == 'block') {
                document.getElementById(cls1[0]).style.display = 'none';
                document.getElementById(cls).src = '<?php echo SURL ?>assets/images/reports/plus.png';
            } else {
                document.getElementById(cls1[0]).style.display = 'block';
                document.getElementById(cls).src = '<?php echo SURL ?>assets/images/reports/minus.png';
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        function export_File() {
            var csvHdr = <?php echo json_encode($csv_hdr); ?>;
            var csvOutput = <?php echo json_encode($csv_output); ?>;

            $.ajax({
                url: "<?php echo SURL . "Common/export_to_xls" ?>",
                cache: false,
                type: "POST",
                data: {
                    csv_hdr: csvHdr,
                    csv_output: csvOutput
                },
                success: function(data) {
                    var blob = new Blob([data]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'Konwa_Report.xls';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            });
        }
    </script>

    <script>
        function exportFile() {
            var csvHdrs = <?php echo json_encode($csv_hdrs); ?>;
            var csvOutputs = <?php echo json_encode($csv_outputs); ?>;

            $.ajax({
                url: "<?php echo SURL . "Common/export" ?>",
                cache: false,
                type: "POST",
                data: {
                    csv_hdrs: csvHdrs,
                    csv_outputs: csvOutputs
                },
                success: function(data) {
                    var blob = new Blob([data]);
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'Konwa_Report.csv';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            });
        }
    </script>

</body>

</html>