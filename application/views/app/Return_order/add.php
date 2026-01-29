<!DOCTYPE html>
<html lang="en">
<?php
$this->load->view('app/include/head');
$this->load->view('app/include/header'); ?>

<body class="no-skin">
    <style>
        .select2-selection--single {
            height: 33px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 28px !important;
        }

        ol,
        ul {
            padding: 0;
            margin: 0 0 10px 0px !important;
        }
    </style>
    <div class="main-container ace-save-state" id="main-container">
        <?php $this->load->view('app/include/sidebar');
        ?>
        <div class="main-content">
            <div class="main-content-inner">
                <div class="breadcrumbs ace-save-state" id="breadcrumbs">
                    <ul class="breadcrumb">
                        <li>
                            <i class="ace-icon fa fa-home home-icon"></i>
                            <a href="<?php echo SURL . "Module/app"; ?>">Home</a>
                        </li>
                        <li>
                            <a href="<?php echo SURL . "app/Return_order"; ?>">Cylinder Return List </a>
                        </li>
                        <li class="active">Cylinder Return </li>
                    </ul><!-- /.breadcrumb -->

                    <div class="nav-search" id="nav-search">
                        <form class="form-search">
                            <span class="input-icon">
                                <input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
                                <i class="ace-icon fa fa-search nav-search-icon"></i>
                            </span>
                        </form>
                    </div><!-- /.nav-search -->
                </div>

                <div class="page-content">
                    <div class="ace-settings-container" id="ace-settings-container">
                        <div class="btn btn-app btn-xs btn-warning ace-settings-btn" id="ace-settings-btn">
                            <i class="ace-icon fa fa-cog bigger-130"></i>
                        </div>

                        <!-- /.ace-settings-box -->
                    </div><!-- /.ace-settings-container -->

                    <div class="page-header">
                        <h1>
                            LPG
                            <small>
                                <i class="ace-icon fa fa-angle-double-right"></i>
                                Cylinder Return
                            </small>
                        </h1>
                    </div><!-- /.page-header -->
                    <style>
                        .scheduler-border {
                            border: 1px solid #ccc;
                            padding: 5px 10px;
                            border-radius: 5px;
                            background: #fff;
                        }

                        fieldset.scheduler-border {
                            padding-bottom: 20px;
                        }

                        legend {
                            width: 100%;
                            margin-bottom: 20px;
                            font-size: 21px;
                            line-height: inherit;
                            border-bottom: 1px solid #e5e5e5;
                        }

                        label {
                            font-weight: bold;
                        }
                    </style>
                    <div class="row">
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->

                            <?php
                            if ($this->session->flashdata('err_message')) {
                            ?>

                                <div class="alert alert-danger">
                                    <button type="button" class="close" data-dismiss="alert">
                                        <i class="ace-icon fa fa-times"></i>
                                    </button>

                                    <strong>
                                        <i class="ace-icon fa fa-times"></i>
                                        Oh snap!
                                    </strong>

                                    <?php echo $this->session->flashdata('err_message'); ?>
                                    <br>
                                </div>

                            <?php
                            } ?>
                            <form id="FormID" class="form-horizontal" role="form" method="post" action="<?php echo SURL; ?>app/Return_order/submit" enctype="multipart/form-data">

                                <div class="col-md-12 form-group">
                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">Order Detail</legend>



                                        <div class="form-group">
                                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1">Date</label>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                    <input name="date" autofocus class="form-control date-picker" id="date" data-date-end-date="0d" type="text" data-date-format="yyyy-mm-dd" required value="<?php echo $return['date'] ? $return['date'] : date('Y-m-d'); ?>">
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar bigger-110"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1">Sale Point</label>
                                            <div class="col-sm-4">
                                                <select class="select-2 form-control" name="salepoint" onchange="fetchData()" id="salepoint">
                                                    <?php foreach ($salepoint as $key => $value) { ?>
                                                        <option value="<?php echo $value['sale_point_id']; ?>" <?php if ($sale_point_id == $value['sale_point_id']) {
                                                                                                                    echo "selected";
                                                                                                                } ?>><?php echo $value['sp_name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1">Select Delivered Order </label>
                                            <div class="col-sm-4">
                                                <select required="required" class="select-2 form-control" name="order" id="order" data-placeholder="Choose a order..." autofocus onchange="get_customer()">
                                                </select>
                                            </div>
                                            <input type="hidden" value="<?php echo $return['order_id']; ?>" id="order_id">
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-md-12 form-group">
                                    <fieldset class="scheduler-border">
                                        <legend class="scheduler-border">Customer / Delivery Info</legend>
                                        <div class="col-xs-8">
                                            <div class="col-xs-12 form-group">
                                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Customer Name</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" tabindex="-1" readonly id="name">
                                                </div>
                                            </div>
                                            <div class="col-xs-12 form-group">
                                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Phone #</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" tabindex="-1" readonly id="phone">
                                                </div>
                                            </div>
                                            <div class="col-xs-12 form-group">
                                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">City</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" tabindex="-1" readonly id="city">
                                                </div>
                                            </div>
                                            <div class="col-xs-12 form-group">
                                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Area</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" tabindex="-1" readonly id="area">
                                                </div>
                                            </div>
                                            <div class="col-xs-12 form-group">
                                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Order Status</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" tabindex="-1" readonly id="order_status_actual">
                                                </div>
                                            </div>
                                            <div class="col-xs-12 form-group">
                                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Delivery Type</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" tabindex="-1" readonly id="delivery_type">
                                                </div>
                                            </div>
                                            <div class="col-xs-12 form-group">
                                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Delivery Charges</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" tabindex="-1" readonly id="delivery_charges">
                                                </div>
                                            </div>
                                            <div class="col-xs-12 form-group">
                                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Delivery Address</label>
                                                <div class="col-sm-6">
                                                    <textarea disabled tabindex="-1" name="delivery_address" id="delivery_address" class="form-control" maxlength="250" style="resize: vertical;"></textarea>
                                                    <textarea disabled tabindex="-1" name="previous_delivery_address" id="previous_delivery_address" class="hidden" maxlength="250" style="resize: vertical;"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 form-group">
                                                <?php
                                                // Fetch zone details
                                                $zoneDetails = $this->db->query("SELECT area as area_id, area_name, zone_id FROM tbl_zone_detail")->result_array();
                                                $polygons = array();
                                                foreach ($zoneDetails as $values) {
                                                    $areaStrings = explode('|', $values['area_id']);

                                                    foreach ($areaStrings as $areaString) {
                                                        $coordinates = explode(',', $areaString);
                                                        $polygon = [];

                                                        for ($i = 0; $i < count($coordinates); $i += 2) {
                                                            $polygon[] = [
                                                                'latitude' => floatval($coordinates[$i]),
                                                                'longitude' => floatval($coordinates[$i + 1])
                                                            ];
                                                        }
                                                        $polygons[] = $polygon;
                                                    }
                                                }
                                                // Convert $polygons array to JSON format
                                                $polygonsJson = json_encode($polygons);

                                                ?>
                                                <label class="col-sm-4 control-label no-padding-right" for="form-field-1">Delivery Location</label>
                                                <div class="col-sm-6">
                                                    <!-- Small map display -->
                                                    <div id="smallMap" disabled style="height: 200px; width: 100%;"></div>
                                                    <input type="hidden" name="delivery_location" id="delivery_location">
                                                    <input type="hidden" name="preivous_delivery_location" id="preivous_delivery_location">

                                                    <!-- Button to open modal for full map -->
                                                    <button type="button" id="modal_fullmap" class="btn btn-primary" data-toggle="modal" data-target="#mapModal" style="display: none;">
                                                        Edit Location
                                                    </button>
                                                </div>
                                            </div>
                                            <style>
                                                .pac-container {
                                                    background-color: #FFF;
                                                    z-index: 20;
                                                    position: fixed;
                                                    display: inline-block;
                                                    float: left;
                                                }

                                                .modal {
                                                    z-index: 20;
                                                }

                                                .modal-backdrop {
                                                    z-index: 10;
                                                }
                                            </style>
                                            <!-- Modal for full map view -->
                                            <div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="mapModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="mapModalLabel">Update Delivery Location</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="modal-body">
                                                                <input id="pac-input" class="controls" type="text" placeholder="Search Box" />
                                                                <div id="fullMap" style="width:100%; height:500px; border:11px ;"></div>
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            <button type="button" class="btn btn-primary" id="saveLocation" data-dismiss="modal">Save Location</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Google Maps API -->
                                            <script async src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCJPePs39ubzYGmfpcKbPV6k404GvXcL7s&libraries=places,geometry" defer></script>


                                            <script>
                                                var markers = [];
                                                var currentLocation = {
                                                    lat: 33.57489122401441,
                                                    lng: 73.15177079290152
                                                }; // Default location

                                                let smallMap, fullMap, marker, fullMarker;
                                                var areaCoordinates = <?php echo $polygonsJson; ?>;
                                                // console.log(areaCoordinates); // Check the structure

                                                // Track whether the selected location is valid
                                                var isLocationValid = false;

                                                // Initialize the small map
                                                function initSmallMap() {
                                                    smallMap = new google.maps.Map(document.getElementById('smallMap'), {
                                                        zoom: 15,
                                                        center: currentLocation
                                                    });

                                                    marker = new google.maps.Marker({
                                                        position: currentLocation,
                                                        map: smallMap,
                                                        title: "Delivery Location"
                                                    });
                                                }

                                                // Initialize the full map
                                                function initFullMap() {
                                                    fullMap = new google.maps.Map(document.getElementById('fullMap'), {
                                                        zoom: 15,
                                                        center: currentLocation
                                                    });

                                                    // Initialize the marker at the current location
                                                    fullMarker = new google.maps.Marker({
                                                        position: currentLocation,
                                                        map: fullMap,
                                                        draggable: true,
                                                        title: "Update Location"
                                                    });

                                                    // Update the current location when the marker is dragged
                                                    fullMarker.addListener('dragend', function(event) {
                                                        validateAndSetLocation(event.latLng.lat(), event.latLng.lng());
                                                    });

                                                    // Setup the search box
                                                    const input = document.getElementById('pac-input');
                                                    const searchBox = new google.maps.places.SearchBox(input);
                                                    fullMap.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                                                    // Listen for search box places_changed event
                                                    searchBox.addListener('places_changed', function() {
                                                        var places = searchBox.getPlaces();
                                                        if (places.length === 0) return;

                                                        // Clear out the old markers
                                                        markers.forEach(function(marker) {
                                                            marker.setMap(null);
                                                        });
                                                        markers = [];

                                                        var bounds = new google.maps.LatLngBounds();
                                                        places.forEach(function(place) {
                                                            if (!place.geometry) return;

                                                            // Create a new marker for the searched place
                                                            var newMarker = new google.maps.Marker({
                                                                map: fullMap,
                                                                icon: 'https://khaadim.com/img/newmarpmarker.png',
                                                                title: place.name,
                                                                position: place.geometry.location
                                                            });
                                                            markers.push(newMarker);

                                                            // Validate the search result location
                                                            validateAndSetLocation(place.geometry.location.lat(), place.geometry.location.lng());

                                                            if (place.geometry.viewport) {
                                                                bounds.union(place.geometry.viewport);
                                                            } else {
                                                                bounds.extend(place.geometry.location);
                                                            }
                                                        });

                                                        fullMap.fitBounds(bounds);
                                                    });
                                                }


                                                // Convert the coordinates to a format usable by Google Maps
                                                function parseCoordinates(areaCoordinates) {
                                                    return areaCoordinates.map(area => area.map(point => ({
                                                        lat: point.latitude,
                                                        lng: point.longitude
                                                    })));
                                                }

                                                // Initialize polygons for small map and full map
                                                function drawPolygonsOnMap(map, polygons) {
                                                    var polygonShapes = [];
                                                    polygons.forEach(function(coordinates) {
                                                        const polygon = new google.maps.Polygon({
                                                            paths: coordinates,
                                                            strokeColor: "#000000",
                                                            strokeOpacity: 0.8,
                                                            strokeWeight: 2,
                                                            fillOpacity: 0,
                                                            map: map
                                                        });

                                                        polygonShapes.push(polygon); // Store polygons to check location inside
                                                    });
                                                    return polygonShapes;
                                                }

                                                function validateAndSetLocation(lat, lng) {
                                                    var selectedLocation = new google.maps.LatLng(lat, lng);

                                                    // Parse the coordinates for all polygons
                                                    var parsedPolygons = parseCoordinates(areaCoordinates);
                                                    isLocationValid = false; // Reset validity

                                                    // Iterate over all polygons to check if the selected location is inside any of them
                                                    parsedPolygons.forEach(function(coordinates) {
                                                        var polygon = new google.maps.Polygon({
                                                            paths: coordinates
                                                        });

                                                        if (google.maps.geometry.poly.containsLocation(selectedLocation, polygon)) {
                                                            isLocationValid = true; // Location is inside a valid polygon
                                                        }
                                                    });

                                                    if (isLocationValid) {
                                                        // If the location is valid, save the location and update the marker
                                                        updateMapLocation(lat, lng);
                                                        var locationData = {
                                                            latitude: lat,
                                                            longitude: lng,
                                                            latitudeDelta: 0.009,
                                                            longitudeDelta: 0.009
                                                        };
                                                        document.getElementById('delivery_location').value = JSON.stringify(locationData);
                                                        // alert("Location updated successfully.");
                                                    } else {
                                                        // If the location is outside the selected polygons, show an alert
                                                        alert("Selected location is outside the allowed delivery areas. Please choose a location within the marked areas.");
                                                    }
                                                }

                                                // Call this function after maps are initialized to draw polygons
                                                function markAreasOnMap() {
                                                    var parsedPolygons = parseCoordinates(areaCoordinates);
                                                    // Draw polygons on both maps
                                                    smallMapPolygons = drawPolygonsOnMap(smallMap, parsedPolygons);
                                                    fullMapPolygons = drawPolygonsOnMap(fullMap, parsedPolygons);
                                                }

                                                // Load small map on page load
                                                window.onload = function() {
                                                    initFullMap();
                                                    initSmallMap();
                                                    markAreasOnMap();
                                                };

                                                // Load full map when modal is shown
                                                $('#mapModal').on('shown.bs.modal', function() {
                                                    setTimeout(function() {
                                                        initFullMap();
                                                        google.maps.event.trigger(fullMap, 'resize');
                                                        fullMap.setCenter(currentLocation); // Ensure correct centering
                                                    }, 300);
                                                });

                                                // Save the updated location when "Save Location" is clicked
                                                document.getElementById('saveLocation').addEventListener('click', function() {
                                                    if (isLocationValid) {
                                                        var locationData = {
                                                            latitude: currentLocation.lat,
                                                            longitude: currentLocation.lng,
                                                            latitudeDelta: 0.009,
                                                            longitudeDelta: 0.009
                                                        };
                                                        document.getElementById('delivery_location').value = JSON.stringify(locationData);
                                                    } else {
                                                        alert("Please select a location within the valid delivery areas.");
                                                    }
                                                });

                                                // Function to update the small map and marker with the new location
                                                function updateMapLocation(latitude, longitude) {
                                                    currentLocation = {
                                                        lat: latitude,
                                                        lng: longitude
                                                    };
                                                    marker.setPosition(currentLocation);
                                                    smallMap.setCenter(currentLocation);
                                                    fullMarker.setPosition(currentLocation);
                                                    fullMap.setCenter(currentLocation);
                                                }
                                            </script>


                                        </div>
                                        <div class="col-xs-4">
                                            <img width="268" height="210" id="target" src="<?php if (isset($record['dp'])) {
                                                                                                echo IMG . 'user/' . $record['dp'];
                                                                                            } else {
                                                                                                echo IMG . 'user/default.JPG';
                                                                                            } ?>" style="margin-left: 0%;">
                                        </div>
                                    </fieldset>
                                </div>
                                <div style="display: none;">
                                    <select class="form-control select-2" id="item_type" data-placeholder="Choose a Item...">
                                    </select>
                                </div>
                                <div class="row col-md-12">
                                    <div class="col-xs-12 col-sm-12 pricing-span-body" style="margin-left: 1%; display: flex;">
                                        <div class="pricing-span6">
                                            <div class="widget-box pricing-box-small widget-color-blue2">
                                                <div class="widget-header">
                                                    <h6 class="widget-title smaller lighter">Item</h6>
                                                </div>
                                                <div class="widget-body">
                                                    <select class="select-2 form-control" id="materialcode" onchange="get_item_detail()" data-placeholder="Choose a Item...">

                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pricing-span5">
                                            <div class="widget-box pricing-box-small widget-color-blue2">
                                                <div class="widget-header">
                                                    <h6 class="widget-title smaller lighter">Category</h6>
                                                </div>

                                                <div class="widget-body">
                                                    <input type="text" class="form-control" id="category" tabindex="-1" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pricing-span4 brands" id="brandContainer" style="display: none;">
                                            <div class="widget-box pricing-box-small widget-color-blue2">
                                                <div class="widget-header">
                                                    <h6 class="widget-title smaller lighter">Cylinder Brand</h6>
                                                </div>
                                                <div class="widget-body">
                                                    <select class="form-control select-2" id="cylinder_brand" onchange="get_swap_charges()" data-placeholder="Choose a Brand...">
                                                        <?php $brands = $this->db->query("SELECT * FROM `tbl_brand`")->result_array();
                                                        foreach ($brands as $key => $value) { ?>
                                                            <option value="<?php echo $value['brand_id'] ?>"><?php echo $value['brand_name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pricing-span5" id="securityChargesContainer">
                                            <div class="widget-box pricing-box-small widget-color-blue2">
                                                <div class="widget-header">
                                                    <h6 class="widget-title smaller lighter">Security Charges</h6>
                                                </div>
                                                <div class="widget-body">
                                                    <input type="text" onkeyup="CalAmount()" maxlength="10" onkeypress="return /[0-9 . ]/i.test(event.key)" class="form-control" id="security_charges" pattern="^[0-9.]+$" title="Only Numbers Allowed...">
                                                    <input type="hidden" maxlength="10" onkeypress="return /[0-9 . ]/i.test(event.key)" class="form-control" id="security_charges_a" pattern="^[0-9.]+$" title="Only Numbers Allowed...">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pricing-span3">
                                            <div class="widget-box pricing-box-small widget-color-blue2">
                                                <div class="widget-header">
                                                    <b>
                                                        <h6 class="widget-title smaller lighter" style="font-size: 10px;">Quantity</h6>
                                                    </b>
                                                </div>

                                                <div class="widget-body">
                                                    <input class="form-control" type="text" id="qty" maxlength="6" onkeypress="return /[0-9]/i.test(event.key)" onkeyup="CalAmount()" pattern="^[0-9]+$" title="Only Numbers Allowed...">
                                                    <input class="form-control" type="hidden" id="qty_a" maxlength="6" onkeypress="return /[0-9 . ]/i.test(event.key)" pattern="^[0-9]+$" title="Only Numbers Allowed...">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pricing-span3">
                                            <div class="widget-box pricing-box-small widget-color-grey">
                                                <div class="widget-header">
                                                    <h6 class="widget-title smaller lighter">Amount</h6>
                                                </div>

                                                <div class="widget-body">
                                                    <input class="form-control" type="text" name="amount" id="amount" disabled="disabled" tabindex="-1">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pricing-span3">
                                            <div class="widget-box pricing-box-small widget-color-green">
                                                <div class="widget-header">
                                                    <h6 class="widget-title smaller lighter" style="margin-left: 25%;"> Action </h6>
                                                </div>
                                                <div class="widget-body" align="center">
                                                    <input style=" height:34px;width: 40% !important;" id="addremove" class="btn btn-xs btn-info" type="button" value="Add">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <style>
                                    table {
                                        width: 100%;
                                        table-layout: auto;
                                        /* Let the table size itself based on content */
                                        border-collapse: collapse;
                                        /* Ensure the table looks neat */
                                    }

                                    th,
                                    td {
                                        padding: 10px;
                                        border: 1px solid #ddd;
                                        /* Optional: adds borders to table cells */
                                        white-space: nowrap;
                                        /* Prevent text from wrapping */
                                        text-align: left;
                                        /* Align text to the left */
                                    }

                                    th {
                                        background-color: #f2f2f2;
                                    }

                                    td input {
                                        width: 100%;
                                        /* Force input fields to take full width of their container */
                                        box-sizing: border-box;
                                        /* Include padding and borders in input width calculation */
                                    }

                                    table.auto-width th,
                                    table.auto-width td {
                                        width: auto;
                                        /* Allow columns to auto-adjust based on content */
                                    }

                                    td {
                                        min-width: 50px;
                                        /* Set a minimum width so that very small columns don’t shrink too much */
                                        max-width: 300px;
                                        /* Set a maximum width to prevent excessively wide columns */
                                        overflow: hidden;
                                        /* Hide overflow if content is too long */
                                    }

                                    td input[type="text"] {
                                        white-space: nowrap;
                                        /* Prevent input text from wrapping */
                                        overflow: hidden;
                                        /* Hide overflow if input content is too long */
                                        text-overflow: ellipsis;
                                        /* Display ellipsis (...) for overflow text */
                                    }
                                </style>
                                <div class="col-md-12">
                                    <div class="table-header">
                                        Order Detail
                                    </div>

                                    <div>
                                        <table id="dynamic-table" class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Sr No</th>
                                                    <th>Item</th>
                                                    <th>Security Charges </th>
                                                    <th>Quantity</th>
                                                    <th>Amount</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="order_items">
                                                <?php foreach ($return_d as $key => $value) :
                                                    $item_name =  $this->db->query("select itemname from `tblmaterial_coding` where materialcode='" . $value['materialcode'] . "'")->row_array()['itemname'];
                                                ?>
                                                    <tr>
                                                        <td><?= $key + 1; ?></td>
                                                        <td><?= $item_name; ?>
                                                            <input type="hidden" class="form-control material_code" name="material_code[]" value="<?= $value['materialcode']; ?>" />

                                                        </td>
                                                        <td>
                                                            <input type="text" readonly tabindex="-1" onkeypress="return /[0-9 . ]/i.test(event.key)" maxlength='10' class="form-control security_charges editable" name="security_charges[]" value="<?= $value['security_charges']; ?>" />
                                                            <input type="hidden" class="form-control security_charges_a" value="<?= $value['security_charges']; ?>" />
                                                        </td>
                                                        <td>
                                                            <input type="text" onkeypress="return /[0-9]/i.test(event.key)" maxlength='6' readonly tabindex="-1" class="form-control qty editable" name="qty[]" value="<?= $value['quantity']; ?>" />
                                                            <input type="hidden" class="form-control order_qty" value="<?= $value['quantity']; ?>" />
                                                        </td>
                                                        <td>
                                                            <input type="text" readonly tabindex="-1" class="form-control amount" name="amount[]" value="<?= $value['security_charges'] * $value['quantity']; ?>" />
                                                        </td>
                                                        <td style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                                                            <button type="button" class="btn btn-info btn-sm edit-row">Edit</button>
                                                            <button type="button" class="btn btn-success btn-sm save-row" style="display:none;">Save</button>
                                                            <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>


                                        </table>

                                        <table style="width:35%; float: right;" id="simple-table" class="table  table-bordered table-hover fc_currency">
                                            <thead>
                                                <tr>
                                                    <th colspan="2">Bill Details <span class="currency"></span> </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="background:#848484; color:#fff">Total Quantity</td>
                                                    <td><input class="form-control" type="text" tabindex="-1" readonly="" id="total_qty" name="total_qty" value="<?php echo $return['total_qty'] ?>"></td>
                                                </tr>
                                                <tr>
                                                    <td style="background:#848484; color:#fff">Receivable Amount</td>
                                                    <td><input class="form-control" type="text" tabindex="-1" readonly="" id="total_amount" name="total_amount" value="<?php echo $return['total_amount'] ?>"></td>
                                                </tr>
                                            </tbody>
                                        </table>


                                    </div>
                                </div>

                               


                                <div class="form-actions center">
                                    <button class="btn btn-info btn-xs btnsubmit" id="submitbtn" style="margin-left: -5%; font-size:20px;">
                                        <i class="ace-icon fa fa-check bigger-110"></i>
                                        Submit
                                    </button>
                                </div>

                                <input type="hidden" id="edit" name="edit" value="<?php echo $return['id']; ?>" />

                            </form>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.page-content -->
            </div>
        </div><!-- /.main-content -->

    </div><!-- /.main-container -->

    <?php
    $this->load->view('app/include/footer');
    $this->load->view('app/include/js');
    ?>
    <!-- Include Select2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <?php $this->load->view('app/include/customer_js.php'); ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select-2').select2();
        });


        fetchData()

        function fetchData() {
            var sale_point_id = $('#salepoint').val();
            var order_id = $('#order_id').val();

            // Fetch vehicles
            $.ajax({
                url: '<?php echo SURL; ?>app/Return_order/get_orders',
                type: 'POST',
                data: {
                    sale_point_id: sale_point_id,
                    order_id: order_id
                },
                success: function(response) {

                    $("#order").html(response);
                    get_customer()
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error while fetching vehicles:', status, error);
                }
            });
        }

        function get_customer() {
            var order = $('#order').val();
            $.ajax({
                url: '<?php echo SURL; ?>app/Return_order/get_customer',
                type: 'POST',
                data: {
                    order: order
                },
                success: function(response) {
                    var res = response.split('|');
                    $("#name").val(res[0]);
                    $("#phone").val(res[1]);
                    $("#city").val(res[2]);
                    $("#area").val(res[3]);
                    $("#delivery_type").val(res[6]);
                    $("#delivery_charges").val(res[7]);
                    $("#delivery_address").html(res[4]);
                    $("#previous_delivery_address").html(res[4]);
                    $("#target").attr('src', "<?php echo IMG . 'profile/' ?>" + res[5]);
                    $("#order_status_actual").val(res[8]);
                    get_items();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error while fetching customer details:', status, error);
                }
            });
        }
        get_items()

        function get_items() {
            var order = $('#order').val();
            $.ajax({
                url: '<?php echo SURL; ?>app/Return_order/get_items',
                type: 'POST',
                data: {
                    order: order
                },
                success: function(response) {

                    $("#materialcode").html(response);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error while fetching vehicles:', status, error);
                }
            });
        }

        function get_item_detail() {
            var materialcode = $('#materialcode').val();
            var order = $('#order').val();
            var sale_point_id = $('#salepoint').val();
            var item_type = $('#item_type').val();
            var cylinder_condition = $('#cylinder_condition').val();
            if (order == '' || order <= 0) {
                alert("Please Select Order !");
                return false;
            }
            $.ajax({
                url: '<?php echo SURL; ?>app/Return_order/get_item_detail',
                type: 'POST',
                data: {
                    materialcode: materialcode,
                    order: order,
                    sale_point_id: sale_point_id,
                    item_type: item_type,
                    cylinder_condition: cylinder_condition
                },
                success: function(response) {
                    var res = response.split('|');
                    $("#category").val(res[0]);
                    $("#security_charges").val(res[1]);
                    $("#security_charges_a").val(res[1]);
                    $('#item_type').html(res[2]);
                    $('#qty').val(res[3]);
                    $('#qty_a').val(res[3]);
                    $('#amount').val(res[4]);
                    $('#securityChargesContainer').show();
                    $('#swapChargesContainer').hide();
                    $('#brandContainer').hide();
                    $('#cylinderConditionContainer').hide();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error while fetching item details:', status, error);
                }
            });
        }

        function CalAmount() {
            var qty = $("#qty").val();
            var price = $("#price").val();
            var security_charges = $("#security_charges").val();
            var item_type = $("#item_type").val();
            if (security_charges > 0) {
                var amount = (qty * security_charges);
            } else {
                var amount = (qty * price);
            }
            if (item_type == 'Swap') {
                var amount = (qty * $("#swap_charges").val());
            }
            $("#amount").val(amount);
        }
        $(document).ready(function() {
            $(".btnsubmit").prop("disabled", true);
            // Add new row and ensure specs consistency
            $('#addremove').click(function() {
                // Collect form data
                var materialCode = $('#materialcode').val();
                var text = $('#materialcode option:selected').text();
                var category = $('#category').val();
                var cylinderBrand = $('#cylinder_brand').val();
                var qty = parseFloat($('#qty').val()) || 0;
                var qty_a = parseFloat($('#qty_a').val()) || 0; // Actual order quantity
                var securityCharges = parseFloat($('#security_charges').val()) || 0;
                var security_charges_a = parseFloat($('#security_charges_a').val()) || 0;
                var amount = $('#amount').val(); // Amount

                // Validation: Check if all fields are filled
                if (!materialCode || !category || !qty || !securityCharges || !amount) {
                    alert("Please fill all required fields.");
                    return false;
                }
                if (qty > qty_a) {
                    alert("Quantity Cannot be greater than the order quantity of this item.");
                    $('#qty_a').val(qty_a);
                    return false;
                }
                if (securityCharges > security_charges_a) {
                    alert("Security Cannot be greater than the order Security of this item.");
                    $('#security_charges').val(security_charges_a);
                    return false;
                }
                a = 0;
                $(".material_code").each(function() {
                    if ($(this).val() == materialCode) {
                        a = 1;
                    }
                });
                if (a == 1) {
                    alert("Item already added");
                    return false;
                }
                var rowCount = $('#order_items tbody tr').length;
                var srno = rowCount + 1;

                // Add new row
                var newRow = `
            <tr>
                <td>${srno}</td>
                <td>${text}
                    <input type="hidden" class="form-control material_code "  name="material_code[]" value="${materialCode}" />
                    <!-- Hidden input to store the original order quantity -->
                    <input type="hidden" class="form-control order_qty" value="${qty_a}" />
                </td>
                <td>
                    <input type="text" readonly tabindex="-1" onkeypress="return /[0-9 . ]/i.test(event.key)" maxlength='10' class="form-control security_charges editable" name="security_charges[]" value="${securityCharges}" />
                    <input type="hidden" class="form-control security_charges_a" value="${securityCharges}" />
                </td>
                <td>
                    <input type="text" onkeypress="return /[0-9]/i.test(event.key)" maxlength='6' readonly tabindex="-1" class="form-control qty editable" name="qty[]" value="${qty}" />
                </td>
                <td>
                    <input type="text" readonly tabindex="-1" class="form-control amount" name="amount[]" value="${amount}" />
                </td>
                <td style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                    <button type="button" class="btn btn-info btn-sm edit-row">Edit</button>
                    <button type="button" class="btn btn-success btn-sm save-row" style="display:none;">Save</button>
                    <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                </td>
            </tr>
        `;

                $('#order_items').append(newRow);
                calculateTotals(); // Recalculate totals after adding a row
                toggleSubmitButton(); // Check if submit should be disabled
            });

            // Handle row editing
            $(document).on('click', '.edit-row', function() {
                var row = $(this).closest('tr');
                row.find('.editable').prop('readonly', false).prop('tabindex', 0);
                row.find('.edit-row').hide();
                row.find('.save-row').show();
                row.find('.qty').focus().select(); // Focus on the quantity field for immediate editing
                $("#submitbtn").prop("disabled", true); // Disable submit button when editing
                toggleSubmitButton(); // Check if submit should be disabled
            });

            // Save row after editing, recalculate amount and update the totals
            $(document).on('click', '.save-row', function() {
                var row = $(this).closest('tr');

                // Get values for validation
                var qty = parseFloat(row.find('.qty').val()) || 0;
                var securityCharges = parseFloat(row.find('.security_charges').val()) || 0;
                var qty_a = parseFloat(row.find('.order_qty').val()) || 0;
                var security_charges_a = parseFloat(row.find('.security_charges_a').val()) || 0;
                if (qty > qty_a) {
                    alert("Quantity Cannot be greater than the order quantity of this item.");
                    parseFloat(row.find('.qty').val(qty_a));
                    return false;
                }
                if (securityCharges > security_charges_a) {
                    alert("Security Cannot be greater than the order Security of this item.");
                    parseFloat(row.find('.security_charges').val(security_charges_a));
                    return false;
                }
                var amount = (qty * securityCharges).toFixed(2);
                row.find('.amount').val(amount);

                row.find('.editable').prop('readonly', true).prop('tabindex', -1);
                row.find('.save-row').hide();
                row.find('.edit-row').show();

                calculateTotals(); // Recalculate totals after saving row
                $("#submitbtn").prop("disabled", false); // Enable submit button after saving
                toggleSubmitButton(); // Check if submit should be disabled
            });

            // Remove a row and update result numbers for the material
            $(document).on('click', '.remove-row', function() {
                var row = $(this).closest('tr');
                row.remove();
                calculateTotals(); // Recalculate totals after removing row
                toggleSubmitButton(); // Check if submit should be disabled
            });

            // Function to calculate total quantity and amount
            function calculateTotals() {
                var totalQty = 0;
                var totalAmount = 0;
                $('#order_items tr').each(function() {
                    var qty = parseFloat($(this).find('.qty').val()) || 0;
                    var amount = parseFloat($(this).find('.amount').val()) || 0;
                    totalQty += qty;
                    totalAmount += amount;
                });
                $('#total_qty').val(totalQty);
                $('#total_amount').val(totalAmount.toFixed(2)); // Format as currency or decimal if needed
            }

            // Function to toggle the "Submit" button state
            function toggleSubmitButton() {
                var isAnyRowEditing = false;
                var rowCount = $('#order_items tbody tr').length + 1;
                // Check if any row is in edit mode (i.e., save button is visible)
                $('#order_items tr').each(function() {
                    if ($(this).find('.save-row').is(':visible')) {
                        isAnyRowEditing = true;
                        return false; // Stop the loop once we find an editing row
                    }
                });
                // Enable the submit button if there are rows and no row is in edit mode
                if (rowCount > 0 && !isAnyRowEditing) {
                    $(".btnsubmit").prop("disabled", false);
                } else {
                    $(".btnsubmit").prop("disabled", true);
                }
            }

            // Initial check to enable/disable submit button when the page loads
            toggleSubmitButton(); // Ensure submit button is properly toggled on page load
        });


        $('#FormID').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this); // Gather form data
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        alert(response.message); // Success message from server
                        window.location.href = '<?php echo SURL . "app/Return_order"; ?>'; // Redirect after success
                    } else {
                        alert(response.message); // Error message from server
                    }
                },
                error: function() {
                    alert("Something Went Wrong");
                }
            });
        });
    </script>
</body>

</html>