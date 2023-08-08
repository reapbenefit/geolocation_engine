require("./bootstrap");

// Language: javascript

var defaultLat = 12.9307735;
var defaultLng = 77.5838302;
var map = null;
var marker = null;
var infowindow = new google.maps.InfoWindow({
    content: "Drag me!",
    maxWidth: 400,
});

$(document).on("ready", function () {
    if ($("#address-map-container").length > 0) {
        initialize_map();
    }

    if ($("#gis-key-container").length > 0) {
        initialize_gis_key_mappings();
    }

    initialize_gis();
});

function initialize_map() {
    $("form").on("keyup keypress", function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    $("#clear-search-option").on("click", function () {
        if (confirm("Are you sure, you want to clear the location?")) {
            clearSearchLocation();
        }
    });

    $("#on-form-submit").on("click", function () {
        if (shouldSubmitForm()) {
            submitLocationForm();
            return true;
        }

        alert("Please select a location");
        return false;
    });

    const geocoder = new google.maps.Geocoder();
    setCurrentLocation();
    setSearchAutocomplete(setSearchInput(), geocoder);
}

function setSearchInput() {
    const searchInput = document.getElementById("address-input");
    const fieldKey = "address";
    const isEdit =
        $("#address-latitude").val() != "" &&
        $("#address-longitude").val() != "";

    const latitude = parseFloat($("#address-latitude").val()) || defaultLat;
    const longitude = parseFloat($("#address-longitude").val()) || defaultLng;

    let attrs = {
        center: { lat: latitude, lng: longitude },
        zoom: 12,
        streetViewControl: false,
    };
    let view = document.getElementById("address-map");
    map = new google.maps.Map(view, attrs);

    setCustomControl(map);

    marker = setMarker(map, latitude, longitude);
    marker.setVisible(isEdit);

    const autocomplete = new google.maps.places.Autocomplete(searchInput);

    let autocompletes = [];
    autocomplete.key = fieldKey;
    autocompletes.push({
        input: searchInput,
        map: map,
        marker: marker,
        autocomplete: autocomplete,
    });

    return autocompletes;
}

function setSearchAutocomplete(autocompletes, geocoder) {
    const input = autocompletes[0].input;
    const autocomplete = autocompletes[0].autocomplete;
    map = autocompletes[0].map;
    marker = autocompletes[0].marker;

    google.maps.event.addListener(autocomplete, "place_changed", function () {
        marker.setVisible(false);
        const place = autocomplete.getPlace();

        geocoder.geocode(
            { placeId: place.place_id },
            function (results, status) {
                if (status === google.maps.GeocoderStatus.OK) {
                    const lat = results[0].geometry.location.lat();
                    const lng = results[0].geometry.location.lng();
                    infowindow.setContent(results[0].formatted_address);
                    setLocationCoordinates(lat, lng);
                    setAddressMetaData(results[0]);
                }
            }
        );

        if (!place.geometry) {
            window.alert(
                "No details available for input: '" + place.name + "'"
            );
            input.value = "";
            return;
        }

        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);
        }
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);
    });
}

function setLocationCoordinates(lat, lng) {
    $("#address-latitude").val(lat);
    $("#address-longitude").val(lng);
}

function setAddressToForm(address) {
    $("#address-input").val(address);
}

function setAddressMetaData(data) {
    let meta_data = JSON.stringify(data);
    $("#address-meta-data").val(meta_data);
}

function geocodePosition(pos, infowindow) {
    geocoder = new google.maps.Geocoder();
    geocoder.geocode(
        {
            latLng: pos,
        },
        function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                let lat = results[0].geometry.location.lat();
                let lng = results[0].geometry.location.lng();
                setAddressToForm(results[0].formatted_address);
                setLocationCoordinates(lat, lng);
                setAddressMetaData(results[0]);
                infowindow.setContent(results[0].formatted_address);
                $("#mapErrorMsg").hide(100);
            } else {
                $("#mapErrorMsg")
                    .html("Cannot determine address at this location." + status)
                    .show(100);
            }
        }
    );
}

function setCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            onCurrentLocationSuccess,
            onCurrentLocationError
        );
    } else {
        alert("geolocation not supported");
    }
}

function onCurrentLocationSuccess(position) {
    let lat = position.coords.latitude;
    let lng = position.coords.longitude;

    // lat = 28.9112338;
    // lng = 79.5158495;

    console.log(lat, lng);
    map.setCenter({ lat: lat, lng: lng });
    setMarker(map, lat, lng);
    setLocationCoordinates(lat, lng);
}

function onCurrentLocationError(msg) {
    clearSearchLocation();
}

function setMarker(map, latitude, longitude) {
    if (!marker) {
        marker = new google.maps.Marker({
            draggable: true,
            map: map,
        });

        infowindow.open(map, marker);
    }

    marker.setPosition({ lat: latitude, lng: longitude });
    marker.setVisible(true);

    geocodePosition(marker.getPosition(), infowindow);

    google.maps.event.addListener(marker, "dragend", function () {
        geocodePosition(marker.getPosition(), infowindow);
    });

    return marker;
}

function clearSearchLocation() {
    $("#address-input").val("");
    $("#address-latitude").val("");
    $("#address-longitude").val("");
    $("#address-meta-data").val("");
}

function shouldSubmitForm() {
    return (
        $("#address-input").val() != "" &&
        $("#address-latitude").val() != "" &&
        $("#address-longitude").val() != ""
    );
}

function submitLocationForm() {
    $("#on-form-submit").hide();
    $("#loading-text").show();
    $("#address-form").trigger("submit");
    return true;
}

function setCustomControl(map) {
    const centerControlDiv = document.createElement("div");

    CenterControl(centerControlDiv, map);

    map.controls[google.maps.ControlPosition.RIGHT_CENTER].push(
        centerControlDiv
    );
}

function CenterControl(controlDiv, map) {
    const controlUI = document.createElement("div");

    controlUI.style.backgroundColor = "#fff";
    controlUI.style.border = "2px solid #fff";
    controlUI.style.borderRadius = "3px";
    controlUI.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
    controlUI.style.cursor = "pointer";
    controlUI.style.marginTop = "10px";
    controlUI.style.marginRight = "10px";
    controlUI.style.textAlign = "center";
    controlUI.title = "Pick you current location";
    controlDiv.appendChild(controlUI);

    // Set CSS for the control interior.
    const controlText = document.createElement("div");

    let padding = "0px";

    controlText.style.color = "rgb(25,25,25)";
    controlText.style.fontFamily = "Roboto,Arial,sans-serif";
    controlText.style.fontSize = "10px";
    controlText.style.lineHeight = "16px";
    controlText.style.paddingTop = padding;
    controlText.style.paddingBottom = padding;
    controlText.style.paddingRight = "4px";
    controlText.style.paddingLeft = "4px";
    controlText.innerHTML =
        '<img src="/icons8-location-48.png" alt="Current Location" style="width: 30px;" />';
    controlUI.appendChild(controlText);

    controlUI.addEventListener("click", () => {
        setCurrentLocation();
    });
}

function initialize_gis() {
    $("#states").on("change", function () {
        appendDropDownValues(
            "state",
            $(this).val(),
            $("#districts"),
            "Select District"
        );
    });

    $("#districts").on("change", function () {
        appendDropDownValues(
            "district",
            $(this).val(),
            $("#cities"),
            "Select City"
        );
    });

    $("#gis_boundary_types").on("change", function () {
        appendDropDownValues(
            "gis_boundary",
            $(this).val(),
            $("#gis_boundary_sub_types"),
            "Select Sub type"
        );
    });
}

function appendDropDownValues(type, id, element, default_value) {
    let url = "/gis/dropdown-values";

    // axios.post(url, { type: type, id: id }).then((response) => {
    //     let options = response.data.map((item) => {
    //         return `<option value="${item.id}">${item.name}</option>`;
    //     });

    //     options =
    //         `<option value=''>${default_value}</option>` + options.join("");
    //     element.html(options);
    // });

    $.ajax({
        url: url,
        type: "POST",
        data: { type: type, id: id },
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            let options = response.map((item) => {
                return `<option value="${item.id}">${item.name}</option>`;
            });

            options =
                `<option value=''>${default_value}</option>` + options.join("");
            element.html(options);
        },
    });
}

// gis key mappings

function initialize_gis_key_mappings() {
    var myModal = document.getElementById("createGisKeyModal");
    myModal.addEventListener("shown.bs.modal", function () {
        $("#gisKey").val("");
        $("#gisBoundarySubTypeId").val("");
        $("#gisKey").focus();
    });

    $("#gis-key-container").on("click", function () {
        $(".error_message").hide();
    });
}
