initializeMap();
var map;
function initializeMap(){
    map = new L.map('map', {
        scrollWheelZoom: false,
        minZoom: 7.2,
        maxZoom: 12
    }).setView([28.39, 84.12], 7.2);
    map.doubleClickZoom.disable();
}


// map.on('mouseover', () => { map.scrollWheelZoom.enable(); });
// map.on('mouseout', () => { map.scrollWheelZoom.disable(); });


var markerClusters = L.markerClusterGroup({
    spiderfyOnMaxZoom: true,
    showCoverageOnHover: true,
    zoomToBoundsOnClick: true
});

hideAllBreadCrumb();

function hideAllBreadCrumb() {
    hideProvinceBreadCrumb();
    hideDistrictBreadCrumb();
    hideLocalLevelBreadCrumb();
}

function hideProvinceBreadCrumb() {
    $('#province-angle-right').hide();
    $('#province_button').parent().hide();
}
function hideDistrictBreadCrumb() {
    $('#district-angle-right').hide();
    $('#district_button').parent().hide();
}
function hideLocalLevelBreadCrumb() {
    $('#locallevel-angle-right').hide();
    $('#locallevel_button').parent().hide();
}

function showProvinceBreadCrumb() {
    $('#province-angle-right').show();
    $('#province_button').parent().show();
}
function showDistrictBreadCrumb() {
    $('#district-angle-right').show();
    $('#district_button').parent().show();
}
function showLocalLevelBreadCrumb() {
    $('#locallevel-angle-right').show();
    $('#locallevel_button').parent().show();
}




// control that shows state info on hover
var info = L.control();

info.onAdd = function (map) {
    this._div = L.DomUtil.create('div', 'info');
    this._div.innerHTML = '<h4 id="country_info"><a onClick="resetToCountry()"><i class="la la-refresh" aria-hidden="true"></i> नेपाल</a></h4>';
    return this._div;
};

info.update = function (props) {
    if (props.Level == 0) {
        markerClusters.clearLayers();
        hideDistrictBreadCrumb();
        hideLocalLevelBreadCrumb();
        showProvinceBreadCrumb();
        $('#province_button').html("<a onClick='resetToProvince(" + props.Province + ")'>" + props.PROVINCE_NAME + "</span></a>");
    }
    if (props.Level == 1) {
        markerClusters.clearLayers();
        hideLocalLevelBreadCrumb();
        showDistrictBreadCrumb();
        $('#district_button').html("<a onClick='resetToDistrict(" + props.District + ")'>" + props.DISTRICT_NAME + "</span></a>");

    }
    if (props.Level == 2) {
        showLocalLevelBreadCrumb();
        $('#locallevel_button').html("<a onClick='resetToLocalLevel(" + props.Locallevel + ")'>" + props.LOCALLEVEL_NAME + "</span></a>");
    }

};
info.addTo(map);


// geting nepal map data for initial map load
$.get("/get-nepal-map-data", function (data) {
    resetToMapLevel(1);
    provinceData = JSON.parse(data);
    geojson = L.geoJson(provinceData, {
        style: provinceStyle,
        onEachFeature: onEachProvinceFeature
    }).addTo(map);
    var nepal = L.geoJSON(provinceData);
    map.fitBounds(nepal.getBounds());
    showLocalLevelProjects(-1,-1);
    $("#country_button").html("<a onClick='resetToCountry()'>नेपाल</span></a>");
    updateGeoData(-1,-1);
});




// function that define what is done in each click on Nepal Map On Province
function onEachProvinceFeature(feature, layer) {
    layer.on({
        'mouseover': function () { highlightFeature(layer) },
        'mouseout': function () { resetHighlight(layer) },
        'click': function () { provinceOnClick(layer) }
    });

    var label = L.marker(layer.getBounds().getCenter(), {
        icon: L.divIcon({
            className: 'province_label',
            html: feature.properties.PROVINCE_NAME,
            iconSize: [70, 15],
        })
    }).addTo(map);
    label.on({
        'click': function () { provinceOnClick(layer) }
    });
}

var markers = [];

function showLocalLevelProjects(level,areaId){
    var url = '/get-all-projects-on-local-level?level='+level+'&&area_id='+areaId;
    markerClusters.clearLayers();
    $.get(url, function (data) {
        if (data.length != 0) {
            projectsData = JSON.parse(data);

            projectsData.forEach(function (project) {
                project_id = project.project_id;
                icon = '/homepage/icons/' + project.icon + '.png';
                project_name = project.project_name;
                fiscal_year = project.fiscal_year;
                project_category = project.project_category;
                central_contribution = project.central_contribution;
                local_level_contribution = project.local_level_contribution;
                other_contribution = project.other_contribution;
                total_cost = parseFloat(central_contribution) + parseFloat(local_level_contribution) + parseFloat(other_contribution);
                project_status = project.project_status;
                lat = project.lat;
                long = project.long;
                project_url = '/admin/ptproject/' + project_id + '/edit';

                var display_icon = L.icon({ iconUrl: icon, iconSize: [20, 25] });

                new_marker = new L.marker([lat, long], { icon: display_icon }).bindPopup(
                    '<b>आयोजनाको नाम : ' + '<font color="green">' + project_name + '</font></b>' + '<br>' +
                    '<b>आर्थिक वर्ष : ' + '<font color="green">' + fiscal_year + '</font></b>' + '<br>' +
                    '<b>आयोजना क्षेत्र : ' + '<font color="red">' + project_category + '</font></b>' + '<br>' +
                    '<b>केन्द्रीय अनुदान :' + '<font color="blue">' + central_contribution + '</font></b>' + '<br>' +
                    '<b>स्थानीय तह साझेदारी :' + '<font color="blue">' + local_level_contribution + '</font></b>' + '<br>' +
                    '<b>अन्य अनुदान :' + '<font color="blue">' + other_contribution + '</font></b>' + '<br>' +
                    '<b>कुल लागत :' + '<font color="blue">' + total_cost + '</font></b>' + '<br>' +
                    '<b>आयोजना स्थिति: ' + '<font color="blue">' + project_status + '</font></b>' + '<br>' +
                    '<b><a href="' + project_url + '" target="_blank"><i class="la la-eye"></i>View Details</a></b>' + '<br>',
                    {
                        autoClose: true,
                        autoPan: false
                    }

                );
              
                markerClusters.addLayer(new_marker);
            });
            map.addLayer(markerClusters);
        }
    });
}

//for province click
function provinceOnClick(layer) {
    var url = '/get-province-data?id=' + layer.feature.properties.Province;
    // getting province data on click
    $.get(url, function (data) {
        if (data.length != 0) {
            resetToMapLevel(1);
            districtData = JSON.parse(data);
            geojson = L.geoJson(districtData, {
                style: districtStyle,
                onEachFeature: onEachDistrictFeature
            }).addTo(map);
            var nepal = L.geoJSON(districtData);
            map.fitBounds(nepal.getBounds());
            info.update(layer.feature.properties);
            //show local level projects only on clicked province;
            showLocalLevelProjects(0,layer.feature.properties.Province);

            updateGeoData(layer.feature.properties.Province, layer.feature.properties.Level);
        } else {
            resetToMapLevel(1);
            map.fitBounds(layer.getBounds());
        }

    });
}

// function that define what is done in each click on District
function onEachDistrictFeature(feature, layer) {
    layer.on({
        'mouseover': function () { highlightFeature(layer) },
        'mouseout': function () { resetHighlight(layer) },
        'click': function () {
            (layer.feature.properties.TMPP_applicable === true) ? districtOnClick(layer) : layer.bindPopup('<font weight="bold"> छानिएको जिल्लामा <font color="red"> " तराई-मधेस समृद्धि कार्यक्रम "</font> लागू छैन !</font>')
        }
    });
    var label = L.marker(layer.getBounds().getCenter(), {
        icon: L.divIcon({
            className: 'district_label',
            html: feature.properties.DISTRICT_NAME,
            iconSize: [70, 15]
        })
    }).addTo(map);
    label.on({
        'click': function () { (layer.feature.properties.TMPP_applicable === true) ? districtOnClick(layer) : layer.bindPopup('<font weight="bold"> छानिएको जिल्लामा <font color="red"> " तराई-मधेस समृद्धि कार्यक्रम "</font> लागू छैन !</font>') }
    });
}

//for district click
function districtOnClick(layer) {
    var url = '/get-district-data?id=' + layer.feature.properties.District;

    $.get(url, function (data) {
        if (data.length != 0) {
            resetToMapLevel(3);
            locallevelData = JSON.parse(data);
            geojson = L.geoJson(locallevelData, {
                style: locallevelStyle,
                onEachFeature: onEachLocalLevelFeature
            }).addTo(map);
            var nepal = L.geoJSON(locallevelData);
            map.fitBounds(nepal.getBounds());
            info.update(layer.feature.properties);

            //show local level projects only on clicked district;
            showLocalLevelProjects(1,layer.feature.properties.District);
            updateGeoData(layer.feature.properties.District, layer.feature.properties.Level);
        } else {
            resetToMapLevel(1);
            map.fitBounds(layer.getBounds());
        }
    });
}


// function that define what is done in each click on Locallevel
function onEachLocalLevelFeature(feature, layer) {
    layer.on({
        'mouseover': function () { highlightFeature(layer) },
        'mouseout': function () { resetHighlight(layer) },
        'click': function () { (layer.feature.properties.TMPP_applicable === true) ? localLevelOnClick(layer) : layer.bindPopup('<font weight="bold"> छानिएको स्थानीय तहमा  <font color="red"> " तराई-मधेस समृद्धि कार्यक्रम "</font> लागू छैन !</font>') }
    });

    var label = L.marker(layer.getBounds().getCenter(), {
        icon: L.divIcon({
            className: 'localLevel_label',
            html: feature.properties.LOCALLEVEL_NAME,
            iconSize: [100, 15]
        })
    }).addTo(map);
    label.on({
        'click': function (e) { (layer.feature.properties.TMPP_applicable === true) ? localLevelOnClick(layer)  : layer.bindPopup('<b>छानिएको स्थानीय तहमा  <font color="red"> " तराई-मधेस समृद्धि कार्यक्रम "</font> लागू छैन !</b>') }
    });
}


//for locallevel click
function localLevelOnClick(layer) {
       //show local level projects only on clicked province;
       map.fitBounds(layer.getBounds());
       showLocalLevelProjects(2,layer.feature.properties.Locallevel);
       info.update(layer.feature.properties);
       updateGeoData(layer.feature.properties.Locallevel, layer.feature.properties.Level);

}

//get style for province 
function provinceStyle(feature) {
    return {
        weight: 3,
        opacity: 1,
        color: 'white',
        dashArray: '3',
        fillOpacity: 0.7,
        fillColor: getProvinceColor(feature.properties.Province)
    };
}

// get color for each province 
function getProvinceColor(id) {
    var color = '';
    switch (id) {
        case 1:
            color = '#5757fa';
            break;
        case 2:
            color = 'green';
            break;
        case 3:
            color = '#f5424e';
            break;
        case 4:
            color = 'orange';
            break;
        case 5:
            color = '#47ff78';
            break;
        case 6:
            color = '#fc68e9';
            break;
        case 7:
            color = '#42f5f2';
            break;
        default:
            color = "#42f5ef"
        // code block
    }
    return color;
}


//get style for district 
function districtStyle(feature) {

    return {
        weight: 2,
        opacity: 1,
        color: 'black',
        dashArray: '5',
        fillOpacity: 0.7,
        fillColor: getDistrictColor(feature.properties.District, feature.properties.TMPP_applicable)
    }
}

// get color for each district 

function getDistrictColor(d, is_tmpp) {

    var color = '';

    if (!is_tmpp) {
        return color = "white";
    }
    switch (d) {
        case 1: case 11: case 21: case 31: case 41: case 51: case 61: case 71:
            color = '#fc6f6f';
            break;
        case 2: case 22: case 32: case 42: case 52: case 62: case 72:
            color = '#f5de49';
            break;
        case 3: case 13: case 23: case 33: case 43: case 53: case 63: case 73:
            color = '#53cc1f';
            break;
        case 4: case 14: case 24: case 34: case 44: case 54: case 64: case 74:
            color = '#1fe076';
            break;
        case 5: case 15: case 25: case 35: case 45: case 55: case 65: case 75:
            color = '#128eb8';
            break;
        case 6: case 16: case 26: case 36: case 46: case 56: case 66: case 76:
            color = '#2f44fa';
            break;
        case 7: case 17: case 27: case 37: case 47: case 57: case 67: case 77:
            color = '#a229f2';
            break;
        case 8: case 18: case 28: case 38: case 48: case 58: case 68:
            color = '#f22c9c';
            break;
        case 9: case 19: case 29: case 39: case 49: case 59: case 69:
            color = '#f0a868';
            break;
        case 10: case 20: case 30: case 40: case 50: case 60: case 70: case 80:
            color = '#feb062';
            break;
        default:
            color = "#107a8b"
        // code block
    }
    return color;
}

//get style for locallevel 
function locallevelStyle(feature) {
    return {
        weight: 2,
        opacity: 1,
        color: 'red',
        dashArray: '3',
        fillOpacity: 0.7,
        fillColor: getLocalLevelColor(feature.properties.Locallevel, feature.properties.TMPP_applicable)
    }
}

// get color for each district 
function getLocalLevelColor(id, is_tmpp) {
    var color = '';

    if (!is_tmpp) {
        return "white";
    }

    switch (id) {
        case 1: case 11: case 21: case 31: case 41: case 51: case 61: case 71: case 81: case 91:
        case 111: case 121: case 131: case 141: case 151: case 161: case 171: case 181: case 191: 
        case 201: case 211: case 221: case 231: case 241: case 251: case 261: case 271: case 281: case 291: 
        case 301: case 311: case 321: case 331: case 341: case 351: case 361: case 371: case 381: case 391: 
        case 401: case 411: case 421: case 431: case 441: case 451: case 461: case 471: case 481: case 491: 
        case 501: case 511: case 521: case 531: case 541: case 551: case 561: case 571: case 581: case 591: 
        case 601: case 611: case 621: case 631: case 641: case 651: case 661: case 671: case 681: case 691: 
        case 701: case 711: case 721: case 731: case 741: case 751:
            color = '#fc6f6f';
            break;
        case 2: case 12: case 22: case 32: case 42: case 52: case 62: case 72: case 82: case 92: 
        case 102: case 112: case 122: case 132: case 142: case 152: case 162: case 172: case 182: case 192: 
        case 202: case 212: case 222: case 232: case 242: case 252: case 262: case 272: case 282: case 292: 
        case 302: case 312: case 322: case 332: case 342: case 352: case 362: case 372: case 382: case 392: 
        case 402: case 412: case 422: case 432: case 442: case 452: case 462: case 472: case 482: case 492: 
        case 502: case 512: case 522: case 532: case 542: case 552: case 562: case 572: case 582: case 592: 
        case 602: case 612: case 622: case 632: case 642: case 652: case 662: case 672: case 682: case 692: 
        case 702: case 712: case 722: case 732: case 742: case 752:
            color = '#f5de49';
            break;
        case 3: case 13:  case 23:  case 33:  case 43:  case 53:  case 63:  case 73:  case 83:  case 93:  
        case 103: case 113: case 123: case 133: case 143: case 153: case 163: case 173: case 183: case 193: 
        case 203: case 213: case 223: case 233: case 243: case 253: case 263: case 273: case 283: case 293: 
        case 303: case 313: case 323: case 333: case 343: case 353: case 363: case 373: case 383: case 393: 
        case 403: case 413: case 423: case 433: case 443: case 453: case 463: case 473: case 483: case 493: 
        case 503: case 513: case 523: case 533: case 543: case 553: case 563: case 573: case 583: case 593: 
        case 603: case 613: case 623: case 633: case 643: case 653: case 663: case 673: case 683: case 693: 
        case 703: case 713: case 723: case 733: case 743: case 753:
            color = '#53cc1f';
            break;
        case 4: case 14: case 24: case 34: case 44: case 54: case 64: case 74: case 84: case 94: 
        case 104: case 114: case 124: case 134: case 144: case 154: case 164: case 174: case 184: case 194: 
        case 204: case 214: case 224: case 234: case 244: case 254: case 264: case 274: case 284: case 294: 
        case 304: case 314: case 324: case 334: case 344: case 354: case 364: case 374: case 384: case 394: 
        case 404: case 414: case 424: case 434: case 444: case 454: case 464: case 474: case 484: case 494: 
        case 504: case 514: case 524: case 534: case 544: case 554: case 564: case 574: case 584: case 594: 
        case 604: case 614: case 624: case 634: case 644: case 654: case 664: case 674: case 684: case 694: 
        case 704: case 714: case 724: case 734: case 744:
            color = '#1fe076';
            break;
        case 5: case 15: case 25: case 35: case 45: case 55: case 65: case 75: case 85: case 95: 
        case 105: case 115: case 125: case 135: case 145: case 155: case 165: case 175: case 185: case 195: 
        case 205: case 215: case 225: case 235: case 245: case 255: case 265: case 275: case 285: case 295: 
        case 305: case 315: case 325: case 335: case 345: case 355: case 365: case 375: case 385: case 395: 
        case 405: case 415: case 425: case 435: case 445: case 455: case 465: case 475: case 485: case 495: 
        case 505: case 515: case 525: case 535: case 545: case 555: case 565: case 575: case 585: case 595: 
        case 605: case 615: case 625: case 635: case 645: case 655: case 665: case 675: case 685: case 695: 
        case 705: case 715: case 725: case 735: case 745:
            color = '#128eb8';
            break;
        case 6: case 16: case 26: case 36: case 46: case 56: case 66: case 76: case 86: case 96: 
        case 106: case 116: case 126: case 136: case 146: case 156: case 166: case 176: case 186: case 196: 
        case 206: case 216: case 226: case 236: case 246: case 256: case 266: case 276: case 286: case 296: 
        case 306: case 316: case 326: case 336: case 346: case 356: case 366: case 376: case 386: case 396: 
        case 406: case 416: case 426: case 436: case 446: case 456: case 466: case 476: case 486: case 496: 
        case 506: case 516: case 526: case 536: case 546: case 556: case 566: case 576: case 586: case 596: 
        case 606: case 616: case 626: case 636: case 646: case 656: case 666: case 676: case 686: case 696: 
        case 706: case 716: case 726: case 736: case 746:
            color = '#2f44fa';
            break;
        case 7: case 17: case 27: case 37: case 47: case 57: case 67: case 77: case 87: case 97: 
        case 107: case 117: case 127: case 137: case 147: case 157: case 167: case 177: case 187: case 197: 
        case 207: case 217: case 227: case 237: case 247: case 257: case 267: case 277: case 287: case 297: 
        case 307: case 317: case 327: case 337: case 347: case 357: case 367: case 377: case 387: case 397: 
        case 407: case 417: case 427: case 437: case 447: case 457: case 467: case 477: case 487: case 497: 
        case 507: case 517: case 527: case 537: case 547: case 557: case 567: case 577: case 587: case 597: 
        case 607: case 617: case 627: case 637: case 647: case 657: case 667: case 677: case 687: case 697: 
        case 707: case 717: case 727: case 737: case 747:
            color = '#a229f2';
            break;
        case 8: case 18: case 28: case 38: case 48: case 58: case 68: case 78: case 88: case 98: 
        case 108: case 118: case 128: case 138: case 148: case 158: case 168: case 178: case 188: case 198: 
        case 208: case 218: case 228: case 238: case 248: case 258: case 268: case 278: case 288: case 298: 
        case 308: case 318: case 328: case 338: case 348: case 358: case 368: case 378: case 388: case 398: 
        case 408: case 418: case 428: case 438: case 448: case 458: case 468: case 478: case 488: case 498: 
        case 508: case 518: case 528: case 538: case 548: case 558: case 568: case 578: case 588: case 598: 
        case 608: case 618: case 628: case 638: case 648: case 658: case 668: case 678: case 688: case 698: 
        case 708: case 718: case 728: case 738: case 748:
            color = '#f22c9c';
            break;
        case 9: case 19: case 29: case 39: case 49: case 59: case 69: case 79: case 89: case 99: 
        case 109: case 119: case 129: case 139: case 149: case 159: case 169: case 179: case 189: case 199: 
        case 209: case 219: case 229: case 239: case 249: case 259: case 269: case 279: case 289: case 299: 
        case 309: case 319: case 329: case 339: case 349: case 359: case 369: case 379: case 389: case 399: 
        case 409: case 419: case 429: case 439: case 449: case 459: case 469: case 479: case 489: case 499: 
        case 509: case 519: case 529: case 539: case 549: case 559: case 569: case 579: case 589: case 599: 
        case 609: case 619: case 629: case 639: case 649: case 659: case 669: case 679: case 689: case 699: 
        case 709: case 719: case 729: case 739: case 749:
            color = '#f0a868';
            break;
        case 10: case 20: case 30: case 40: case 50: case 60: case 70: case 80: case 90: case 100: 
        case 110: case 120: case 130: case 140: case 150: case 160: case 170: case 180: case 190: case 200: 
        case 210: case 220: case 230: case 240: case 250: case 260: case 270: case 280: case 290: case 300: 
        case 310: case 320: case 330: case 340: case 350: case 360: case 370: case 380: case 390: case 400: 
        case 410: case 420: case 430: case 440: case 450: case 460: case 470: case 480: case 490: case 500: 
        case 510: case 520: case 530: case 540: case 550: case 560: case 570: case 580: case 590: case 600: 
        case 610: case 620: case 630: case 640: case 650: case 660: case 670: case 680: case 690: case 700: 
        case 710: case 720: case 730: case 740: case 750:
            color = '#feb062';
            break;
    }
    return color;
}


//highlight area on mouse-over
function highlightFeature(layer) 
{
    layer.setStyle({
        weight: 3,
        color: 'black',
        dashArray: '3',
        fillOpacity: 0.7,
    });

    if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
        layer.bringToFront();
    }

}

var geojson;
// functions for various reset activity
function resetHighlight(layer) {
    var level = layer.feature.properties.Level;
    if(level === 0){
        layer.setStyle(provinceStyle(layer.feature));
    }else if(level === 1){
        layer.setStyle(districtStyle(layer.feature));
    }else if(level === 2){
        layer.setStyle(locallevelStyle(layer.feature));
    };
}


function resetToMapLevel(level) {
    if (level == 1) {
        map.eachLayer(function (layer) {
            try {
                if (layer.options.icon.options.className != "province_label") {
                    map.removeLayer(layer);
                }
            } catch (err) {

            }
            try {
                if (layer.feature.properties.Level != 0) {
                    map.removeLayer(layer);

                }
            } catch (err) {

            }
            // map.removeLayer(layer);
        });
    }
    if (level == 2) {
        map.eachLayer(function (layer) {
            try {
                if (layer.options.icon.options.className == "district_label") {
                    map.removeLayer(layer);
                }
            } catch (err) {

            }
            try {
                if (layer.feature.properties.Level == 1) {
                    map.removeLayer(layer);
                }
            } catch (err) {

            }
            // map.removeLayer(layer);
        });
    }

    if (level == 3) {
        map.eachLayer(function (layer) {
            try {
                if (layer.options.icon.options.className == "localLevel_label") {
                    map.removeLayer(layer);
                }
            } catch (err) {

            }
            try {
                if (layer.feature.properties.Level == 2) {
                    map.removeLayer(layer);
                }
            } catch (err) {

            }
            // map.removeLayer(layer);
        });
    }
}

//remove all layers;
function removeLayer(){
    map.eachLayer(function (layer) {
        map.removeLayer(layer);
    });
}

//reset current state to original state(country-with-province-state)
function resetToCountry() {
    markerClusters.clearLayers();
    resetToMapLevel(1);
    removeLayer();
    $.get("/get-nepal-map-data", function (data) {
        provinceData = JSON.parse(data);
        geojson = L.geoJson(provinceData, {
            style: provinceStyle,
            onEachFeature: onEachProvinceFeature
        }).addTo(map);
        var nepal = L.geoJSON(provinceData);
        map.fitBounds(nepal.getBounds());
        hideAllBreadCrumb();
        showLocalLevelProjects(-1,-1);
        updateGeoData(-1,-1);
    });
}


// reset action for province
function resetToProvince(id) {
    resetToMapLevel(3);
    resetToMapLevel(2);
    hideDistrictBreadCrumb();
    hideLocalLevelBreadCrumb();
    markerClusters.clearLayers();

    var url = '/get-province-data?id=' + id;
    // getting province data on click
    $.get(url, function (data) {
        $('#district_info').remove();
        $('#local_info').remove();
        districtData = JSON.parse(data);
        geojson = L.geoJson(districtData, {
            style: districtStyle,
            onEachFeature: onEachDistrictFeature
        }).addTo(map);
        var nepal = L.geoJSON(districtData);
        map.fitBounds(nepal.getBounds());
         //show local level projects only on clicked province;
        showLocalLevelProjects(0,id);
        updateGeoData(id, 0);
    });
}

// reset action for district
function resetToDistrict(id) {
    resetToMapLevel(3);
    hideLocalLevelBreadCrumb();
    markerClusters.clearLayers();

    var url = '/get-district-data?id=' + id;
    $.get(url, function (data) {
        locallevelData = JSON.parse(data);
        geojson = L.geoJson(locallevelData, {
            style: locallevelStyle,
            onEachFeature: onEachLocalLevelFeature
        }).addTo(map);
        var nepal = L.geoJSON(locallevelData);
        map.fitBounds(nepal.getBounds());
        //show local level projects only on clicked district;
        showLocalLevelProjects(1,id);
        updateGeoData(id, 1);
    });
}

// reset action for locallevel
function resetToLocalLevel(id) {
    markerClusters.clearLayers();

    var url = '/get-projects-on-local-level?id=' + id;
    $.get(url, function (data) {
        if (data.length != 0) {
            projectsData = JSON.parse(data);

            projectsData.forEach(function (project) {
                project_id = project.project_id;
                icon = '/homepage/icons/' + project.icon + '.png';
                project_name = project.project_name;
                project_category = project.project_category;
                central_contribution = project.central_contribution;
                local_level_contribution = project.local_level_contribution;
                other_contribution = project.other_contribution;
                total_cost = parseFloat(central_contribution) + parseFloat(local_level_contribution) + parseFloat(other_contribution);
                project_status = project.project_status;
                lat = project.lat;
                long = project.long;
                project_url = '/admin/ptproject/' + project_id + '/edit';

                var display_icon = L.icon({ iconUrl: icon, iconSize: [20, 25] });

                new_marker = new L.marker([lat, long], { icon: display_icon }).bindPopup(
                    '<b>आयोजनाको नाम: ' + '<font color="green">' + project_name + '</font></b>' + '<br>' +
                    '<b>आयोजना क्षेत्र: ' + '<font color="red">' + project_category + '</font></b>' + '<br>' +
                    '<b>केन्द्रीय अनुदान :' + '<font color="blue">' + central_contribution + '</font></b>' + '<br>' +
                    '<b>स्थानीय तह साझेदारी :' + '<font color="blue">' + local_level_contribution + '</font></b>' + '<br>' +
                    '<b>अन्य अनुदान :' + '<font color="blue">' + other_contribution + '</font></b>' + '<br>' +
                    '<b>कुल लागत :' + '<font color="blue">' + total_cost + '</font></b>' + '<br>' +
                    '<b>आयोजना स्थिति: ' + '<font color="blue">' + project_status + '</font></b>' + '<br>' +
                    '<b><a href="' + project_url + '" target="_blank"><i class="la la-eye"></i>View Details</a></b>' + '<br>',
                    {
                        autoClose: true,
                        autoPan: false
                    }

                );
              
                markerClusters.addLayer(new_marker);
            });
            map.addLayer(markerClusters);
            // updateGeoData(layer.feature.properties.Province, e.target.feature.properties.Level);
        } else {

        }
    });
}

//get GeoData

function updateGeoData(id,level){
    var url = '/get-geodata?id='+id+'&&level='+level;

    $.get(url,function (data){
        //data for fed_area section
        //shoe fed level information only if map_level is below 2
        if(data.level === 2){
            $('#fed_area').hide();

        }else{
            $('#fed_area').show();

            var district_div = $('#total_district_count').parent().parent().parent();
            if(data.level === 1){
                district_div.hide();
            }else{
                district_div.show();
                $('#total_district_count').text(data.count.districts_count);
            }

            $('#metro_count').text(data.count.metro_count);
            $('#sub_metro_count').text(data.count.sub_metro_count);
            $('#mun_count').text(data.count.mun_count);
            $('#rural_mun_count').text(data.count.rural_mun_count);
            $('#total_local_level_count').text(data.count.total_local_level_count);
        }
        //data for project count summary
        $('#new_projects_demand').text(data.count.new_projects_count);
        $('#selected_projects').text(data.count.selected_projects_count);
        $('#work_in_progress_projects').text(data.count.wip_projects_count);
        $('#completed_projects').text(data.count.completed_projects_count);

        //province-wise project count and cost
        var province_row_number_html = '';
        var province_name_html = '';
        var province_data_count_html = '';
        var province_data_amount_html = '';
        var total_category_projects_count = 0;

        $.each(data.province_projects.main, function (index,row) {
            province_row_number_html += '<div class="text-title">'+row.sn+'</div>'
            province_name_html += '<div class="text-title text-left" id="province_'+row.province_id+'">'+row.name_lc+'</div>'; 
            province_data_count_html += '<div class="text-blue text-value" id="province_'+row.province_id+'">'+row.total_project+'</div>';

             //total_projects_count
             total_category_projects_count += row.total_project;

            //format currency
            formatted_currency = OSREC.CurrencyFormatter.format(row.project_cost, { currency: 'INR',pattern:'रु ,##,##,##,###' });

            province_data_amount_html += '<div class="text-blue text-value text-amount" id="province_'+row.province_id+'">'+formatted_currency+'</div>'; 
        });

        //for total cost
        province_name_html += '<div class="text-blue text-value total_amount_sum">जम्मा</div>';
        province_data_count_html += '<div class="text-blue text-value total_amount_sum">'+total_category_projects_count+'</div>';
        let total_province_projects_cost = OSREC.CurrencyFormatter.format(data.province_projects.total_project_cost, { currency: 'INR',pattern:'रु ,##,##,##,###' });
        province_data_amount_html += '<div class="text-blue text-value text-amount total_amount_sum">'+total_province_projects_cost+'</div>';
        //for brand-card heading
        if(data.level === -1){
            $('#project_province_title').html('प्रदेश अनुसार आयोजना तथ्यांक');
            $('#table_level_title').html('प्रदेश');
            var chart_title = 'प्रदेश अनुसार आयोजना';
        }else if(data.level === 0 && data.province_projects.main.length > 0){
            $('#project_province_title').html( data.province_projects.main[0].name_lc+' को आयोजना तथ्यांक');
            $('#table_level_title').html('प्रदेश');
            var chart_title = data.province_projects.main[0].name_lc+' को आयोजना';
        }else if(data.level === 1 && data.province_projects.main.length > 0){
            $('#project_province_title').html( data.province_projects.main[0].name_lc+' जिल्लाको आयोजना तथ्यांक');
            $('#table_level_title').html('जिल्ला');
            var chart_title = data.province_projects.main[0].name_lc+' जिल्लाको आयोजना';
        }else if(data.level === 2 && data.province_projects.main.length > 0){
            $('#project_province_title').html( data.province_projects.main[0].name_lc+'को आयोजना तथ्यांक');
            $('#table_level_title').html('स्थानीय तह');
            var chart_title = data.province_projects.main[0].name_lc+'को आयोजना';
        }

        $('#province_row_number').html(province_row_number_html);
        $('#province_name').html(province_name_html);
        $('#province_project_count').html(province_data_count_html);
        $('#province_project_amount').html(province_data_amount_html);

         //for building province-projects-charts
         createChart('project_by_province_chart', chart_title, data.province_projects.chart, 'bar');


        //category-wise project count and cost
        var category_row_number_html = '';
        var category_name_html = '';
        var category_data_count_html = '';
        var category_data_amount_html = '';
        var total_province_projects_count = 0;
        $.each(data.category_projects.main, function (index,row) {
            category_row_number_html += '<div class="text-title">'+row.sn+'</div>'
            category_name_html += '<div class="text-title text-left" id="category_'+row.category_id+'">'+row.name_lc+'</div>'; 
            category_data_count_html += '<div class="text-blue text-value" id="category_'+row.category_id+'">'+row.total_project+'</div>';

            //total_projects_count
            total_province_projects_count += row.total_project;

            //format currency
            formatted_currency = OSREC.CurrencyFormatter.format(row.category_cost, { currency: 'INR',pattern:'रु ,##,##,##,###' });
          
            category_data_amount_html += '<div class="text-blue text-value text-amount" id="category_'+row.category_id+'">'+formatted_currency+'</div>'; 
        });

             //for total cost
             category_name_html += '<div class="text-blue text-value total_amount_sum">जम्मा</div>';
             category_data_count_html += '<div class="text-blue text-value total_amount_sum">'+total_province_projects_count+'</div>';
             let total_category_projects_cost = OSREC.CurrencyFormatter.format(data.category_projects.total_project_cost, { currency: 'INR',pattern:'रु ,##,##,##,###' });
             category_data_amount_html += '<div class="text-blue text-value text-amount total_amount_sum">'+total_category_projects_cost+'</div>';

        //for brand-card heading
        if(data.level === -1){
            $('#project_category_title').html('आयोजना क्षेत्र अनुसार आयोजना तथ्यांक');
        }else if(data.level === 0 && data.province_projects.main.length > 0){
            $('#project_category_title').html( data.province_projects.main[0].name_lc+' को आयोजना क्षेत्र अनुसार तथ्यांक');
        }else if(data.level === 1 && data.province_projects.main.length > 0){
            $('#project_category_title').html( data.province_projects.main[0].name_lc+' जिल्लाको आयोजना क्षेत्र अनुसार तथ्यांक');
        }else if(data.level === 2 && data.province_projects.main.length > 0){
            $('#project_category_title').html( data.province_projects.main[0].name_lc+'को आयोजना क्षेत्र अनुसार तथ्यांक');
        }

        $('#category_row_number').html(category_row_number_html);
        $('#category_name').html(category_name_html);
        $('#category_project_count').html(category_data_count_html);
        $('#category_project_amount').html(category_data_amount_html);

       
        createChart('project_by_category_chart', 'आयोजना क्षेत्र अनुसार आयोजना', data.category_projects.chart, 'bar');

    });
}


// element id , title of chart , data, type
function createChart(element_id, title, data, type) {
    var parent_div = $('#' + element_id).parent();
    $('#' + element_id).remove();
    parent_div.append('<canvas id="' + element_id + '" height="270"></canvas>');
    var ctx = document.getElementById(element_id);
    var customBackgroundColor;
    var tooltip_label1 = '* आयोजना संख्या : ';
    var tooltip_label2 = '* कुल लागत : ';

    if(element_id === 'project_by_province_chart'){
         customBackgroundColor = ['brown','green','red','orange','purple','blue','skyblue'];
    }else{
         customBackgroundColor = ['red','blue','green','purple','orange','brown','real','lightgreen','skyblue'];
    }
    var myChart = new Chart(ctx, {
        type: type,
        data: {
            labels: data.labels,
            datasets: [{    
                label: title,
                data: data.data,
                cost :data.cost,
                maxBarThickness: 20,
                backgroundColor: customBackgroundColor,
            }]
        },
        options: {
            responsive: true,
            title: {
                display: true,
                text: title,
                fontSize: 18,
                fontColor:'black',
                fontFamily:'Kalimati',
            },
            animation: {
                animateScale: true,
                animateRotate: true
            },
            tooltips: {
                enabled :true,
                mode: 'single',
                displayColors:false,
                titleFontSize:14,
                titleFontFamily:'Kalimati',
                bodyFontSize:13,
                bodyFontFamily:'Kalimati',
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label1 = tooltip_label1;
                        label1 += data.datasets[0].data[tooltipItem.index];

                        var amount = OSREC.CurrencyFormatter.format(data.datasets[0].cost[tooltipItem.index], { currency: 'INR',pattern:'रु ,##,##,##,###' });
                        var label2 = tooltip_label2;
                        label2 += amount;
                        return [label1,label2];
                    },
                }
            },
            legend: {
                display: false,
                position:'bottom',
                labels: {
                    fontColor: 'black',
                    fontFamily:'Kalimati',
                    fontSize:15
                }
            },
            scales: {
                yAxes: [{
                    display: true,
                    ticks: {
                        beginAtZero: true,
                        fontFamily:'Kalimati',
                        fontColor:'black'
                    },
                }],
            }
        },
    });

}


// show loading spinner
$(document)
    .ajaxStart(function () {
        $('#custom_spinner').show();
    })
    .ajaxStop(function () {
        $('#custom_spinner').hide();
    });