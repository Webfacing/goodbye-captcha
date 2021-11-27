jQuery( document ).ready(function($) {
	
	
    if ($("#flot-container").length > 0)
        initializeModulesPage();
    else
        initializeDashboard();

    function initializeDashboard()
    {
    	
    
        displayDashBoardAttemptsChart();
        displayAttemptsPerModuleAndSectionChart();
        displayLatestAttemptsTable();
        displayLocationsOnMap();
        displayAttemptsPerClientIpTable();

        //displayPercentagePieChart();
        displayTotalAttemptsPerModule()
    }


    function initializeModulesPage()
    {
        displayModulesChart();
        displayModulesTables();
    }

    function displayModulesTables()
    {
        $("[id^='wid-id-']").each(function() {
            var moduleId = $(this).attr('id').substr(7);
            displayModuleTable(moduleId, 1, 'CreatedDate');
        });
    }

    function displayAttemptsPerModuleAndSectionChart()
    {
        var ajaxData = {};
        ajaxData['action']       = 'retrieveAttemptsPerModuleAndSection';
        ajaxData['ajaxRequestNonce'] = GdbcAdmin.ajaxRequestNonce;

        $.ajax({
            type: "post",
            cache: false,
            dataType: "json",
            url: GdbcAdmin.ajaxUrl,
            data: ajaxData,
            success: function (response) {
								
                Morris.Bar({
                    element: 'gdbc-barchart-holder',
                    data: response,
                    hoverCallback: function(index, options, content) {

                        var moduleInfo  = response[index];

                        if(!moduleInfo.attempts)
                        return '';

                        var label = moduleInfo.module;
                        if(moduleInfo.section.length)
                        label += '/' + moduleInfo.section;

                        var hover = '<div>';

                        hover += '<p>' + label + '</p>';
                        hover += '<p><span>' + moduleInfo.attempts + ' attempts' + '</span></p>';

                        hover += '</div>';
                        return hover;
                    },
                xkey: 'y',
                    ykeys: ['attempts'],
                    grid:true,
                    hideHover:'auto',
                    labels: ['Series A', 'Series B']
                });
            }
        });

    }


    function displayAttemptsPerClientIpTable(pageNumber)
    {
        if(typeof pageNumber === 'undefined')
            pageNumber = 1;

        var tableBody = $('#ip-attempts-holder table tbody');
        if (tableBody.length === 0)
            return;

        var ajaxData = {};
        ajaxData['action']           = 'retrieveAttemptsPerClientIp';
        ajaxData['pageNumber']       = pageNumber;
        ajaxData['ajaxRequestNonce'] = GdbcAdmin.ajaxRequestNonce;


        $.ajax({
            type: "post",
            cache: false,
            dataType: "json",
            url: GdbcAdmin.ajaxUrl,
            data: ajaxData,
            success: function (response) {
                            
                tableBody.empty();

                var totalPages = 0;

                $.each(response, function(index, attempt) {

                    var row = $('<tr></tr>');

                    row.append($('<td>' + attempt.Country + '</td>'));

                    var clientIpHtml = attempt.IsIpBlocked ? '<i class="glyphicon glyphicon-minus-sign icon-danger" style="margin-right: 5px"></i>' : '<i class="glyphicon glyphicon-eye-open" style="margin-right: 5px"></i>';


                    row.append($('<td>' + clientIpHtml + attempt.ClientIp + '</td>'));

                    row.append($('<td>' + attempt.Attempts + '</td>'));

                    var blockCell = $('<td></td>');
                    blockCell.append(createBlockIpButtonGroup(attempt.ClientIp, !attempt.IsIpBlocked));
                    row.append(blockCell);

                    tableBody.append(row);

                    totalPages = attempt.Pages;
                })

                $('#ip-attempts-holder div.module-pagination').empty();
                $('#ip-attempts-holder div.module-pagination').append(showPagination(pageNumber, totalPages));

                $('#ip-attempts-holder div.module-pagination li[class!="disabled"] a').on('click', function(){
                    var newPage = $(this).html();
                    if (newPage == '...' || newPage == pageNumber)
                        return;
                    else if (newPage == 'Next')
                        newPage = pageNumber + 1;
                    else if (newPage == 'Prev')
                        newPage = pageNumber - 1;

                    displayAttemptsPerClientIpTable(newPage);

                    return false;
                });

            }
        });
    }



    function displayModuleTable(moduleId, pageNumber, $orderBy)
    {
        if (moduleId == null || moduleId < 1 || moduleId >= 100)
            return;

        var ajaxData = {};
        ajaxData['action']       = 'retrieveDetailedAttemptsPerModule';
        ajaxData['moduleId']     = moduleId;
        ajaxData['pageNumber']   = pageNumber;
        ajaxData['orderBy']      = $orderBy;
        ajaxData['ajaxRequestNonce'] = GdbcAdmin.ajaxRequestNonce;

        $.ajax({
            type : "post",
            cache: false,
            dataType : "json",
            url : GdbcAdmin.ajaxUrl,
            data : ajaxData,
            success: function(response) {
								
                $.each(response, function(prop, moduleData) {
                    if ('PaginationInfo' == prop) {

                        if (moduleData == 0) {
                            $('#wid-id-' + moduleId + ' table tbody').html('<td style="text-align: center; padding-top: 15px">No records found</td>');
                            return;
                        }

                        $('#mp-' + moduleId).empty();
                        $('#mp-' + moduleId).append(showPagination(moduleData[0], moduleData[1]));
                        $('#wid-id-' + moduleId + ' .module-pagination li[class!="disabled"] a').on('click', function(){
                            var newPage = $(this).html();
                            if (newPage == '...' || newPage == pageNumber)
                                return;
                            else if (newPage == 'Next')
                                newPage = parseInt(pageNumber, 10) + 1;
                            else if (newPage == 'Prev')
                                newPage = parseInt(pageNumber, 10) - 1;

                            var modId = $(this).parent().parent().parent().attr('id').substr(3);

                            displayModuleTable(modId , newPage, '');

                            return false;
                        });

                    }

                    if ('ModuleDataHeader' == prop)
                        displayModuleTableHeader('wid-id-' + moduleId, moduleData);

                    if ('ModuleDataRows' == prop)
                        displayModuleTableBody('wid-id-' +moduleId, response);

                });

            }
        });
    }

    function displayModuleTableHeader(containerId, dataValues)
    {
        var tableHeader = $('#' + containerId + ' table thead tr');
        if (tableHeader.length == 0)
            return;
        tableHeader.empty();
        $.each(dataValues, function(k,v){
            var headerCell = $('<th>' + v + '</th>');
            tableHeader.append(headerCell);
        });
        tableHeader.append('<th></th>');
    }

    function displayModuleTableBody(containerId, ajaxResponse)
    {

        var tableBody = $('#' + containerId + ' table tbody');
        if (tableBody.length === 0)
            return;

        tableBody.empty();

        $.each(ajaxResponse.ModuleDataRows, function (index, tableData) {
            var row = $('<tr></tr>');
            var clientIp = 'N/A';
            $.each(ajaxResponse.ModuleDataHeader, function (key, header) {

                if(typeof tableData[key] === 'undefined')
                    tableData[key] = 'N/A';


                if(key === 'ClientIp') {
                    clientIp = tableData[key];
                    if(tableData.IsIpBlocked) {
                        tableData[key] = '<i class="glyphicon glyphicon-minus-sign icon-danger" style="margin-right: 5px"></i>' + tableData[key];
                    }
                    else{
                        tableData[key] = '<i class="glyphicon glyphicon-eye-open" style="margin-right: 5px"></i>' + tableData[key];
                    }
                }
                var cell = $('<td>' + tableData[key] + '</td>');

                row.append(cell);

            });

            var blockCell = $('<td></td>');
            blockCell.append(createBlockIpButtonGroup(clientIp, !tableData.IsIpBlocked));
            row.append(blockCell);

            tableBody.append(row);


        });

    }

    function showPagination(pageNumber, totalPages)
    {
        var ulContainer  = $('<ul class="pagination pagination-sm"></ul>');
        var firstPageLi  = $('<li><a>1</a></li>');
        var lastPageLi   = $('<li><a>' + totalPages + '</a>');
        var previousLi   = $('<li><a>Prev</a></li>');
        var nextLi       = $('<li><a>Next</a></li>');
        var separatorLi1 = $('<li><a>...</a></li>');
        var separatorLi2 = $('<li><a>...</a></li>');

        var currentPageLi = $('<li class="active"><a>' + pageNumber + '</a></li>');

        pageNumber = parseInt(pageNumber, 10);
        totalPages = parseInt(totalPages, 10);

        if (totalPages == 1) {
            firstPageLi = null;
            lastPageLi = null;
            previousLi.addClass('disabled');
            nextLi.addClass('disabled');
        }
        else if (pageNumber == 1) {
            previousLi.addClass('disabled');
            firstPageLi = null;
        }
        else if (pageNumber == totalPages) {
            nextLi.addClass('disabled');
            lastPageLi = null;
        }

        var numberOfAdditionalPages = 1; // = 2; two additional pages in the left, two additional pages in the right (if available)
        var prevPagesArray = new Array();
        var nextPagesArray = new Array();

        for (var i = 0; i < numberOfAdditionalPages; i++ ){
            if (pageNumber - i > 2)
                prevPagesArray[i] = $('<li><a>' + (pageNumber - i - 1) + '</a></li>');

            if ((pageNumber + i + 1) < totalPages)
                nextPagesArray[i] = $('<li><a>' + (pageNumber + i + 1) + '</a></li>');
        }

        ulContainer.append(previousLi);

        if (firstPageLi != null) {
            ulContainer.append(firstPageLi);
            if (prevPagesArray.length > 0)
            {
                var firstPageNumber = parseInt(firstPageLi.text(), 10);
                var nextPageNumber  = parseInt(prevPagesArray[0].text(), 10);
                if ( (nextPageNumber -  firstPageNumber) > 1)
                    ulContainer.append(separatorLi1);
            }
        }
        for(var i = numberOfAdditionalPages-1; i >= 0; i--)
            ulContainer.append(prevPagesArray[i]);

        ulContainer.append(currentPageLi);

        for(var i = 0; i < numberOfAdditionalPages; i++)
            ulContainer.append(nextPagesArray[i]);

        if (lastPageLi != null) {
            if (nextPagesArray.length > 0)
            {
                var lastPageNumber = lastPageLi.text();
                var prevPageNumber = nextPagesArray[nextPagesArray.length - 1].text();
                if ((parseInt(lastPageNumber, 10) -  parseInt(prevPageNumber, 10)) > 1)
                    ulContainer.append(separatorLi2);
            }
            ulContainer.append(lastPageLi);
        }

        ulContainer.append(nextLi);

        return ulContainer;
    }

    function displayModulesChart()
    {
        var toggles  = $("#modules-chart");
        var target = $("#flot-container");

        var data = [];

        var options = {
            grid : {
                hoverable : true
            },
            tooltip : true,
            tooltipOpts : {
                content: '%s: %y',
                //dateFormat: '%b %y',
                defaultTheme : false
            },
            legend: {show: false},
            xaxis : {
                mode : "time"
            },
            yaxes : {
                tickFormatter : function(val) {
                    return "$" + val;
                },
                max : 1200
            }

        };


        var ajaxData = {};
        ajaxData['action']      = 'retrieveDetailedAttemptsForChart';
        ajaxData['ajaxRequestNonce'] = GdbcAdmin.ajaxRequestNonce;
        $.ajax({
            type : "post",
            cache: false,
            dataType : "json",
            url : GdbcAdmin.ajaxUrl,
            data : ajaxData,
            success: function(response){
               	               
                $.each(response, function(prop, modulesData){
                    if (prop === 'ModulesDescriptionArray' && $("#flot-container").length) {
                        var i = 0;
                        $.each(modulesData, function(key, value){
                            var label = $('<label class="checkbox" for="gra-' + i + '"></label>');
                            var input = $('<input type="checkbox" checked="checked" id="gra-' + i + '" name="gra-' + i + '">');
                            var italic = $('<i></i>');
                            label.append(input, italic, value);
                            $(".inline-group").append(label);
                            i++;
                        });
                    } else if (prop === 'ModulesAttemptsArray' && $("#flot-container").length) {

                        var colorArray = ['#931313', '#638167', '#65596B', '#60747C', '#B09B5B', '#3276B1', '#C0C0C0', '#FDDC9A', '#575FB5', '#57B599', '#46CC41', '#C93A24'];
                        var i = 0;
                        $.each(modulesData, function(key, value){
                            var $graphObj = {
                                label : '%x - ' + $('#gra-'+i).parent().text() + ' attempts',
                                data : value,
                                color : colorArray[i],
                                lines : {
                                    show : true,
                                    lineWidth : 3
                                },
                                points : {
                                    show : true
                                }
                            };
                            data[i] = $graphObj;
                            i++;
                        });
                    }

                });
                toggles.find(':checkbox').on('change', function() {
                    plotNow();
                });
                plotNow()
            }
        });



        var plot2 = null;

        function plotNow() {
            var d = [];
            toggles.find(':checkbox').each(function() {
                if ($(this).is(':checked')) {
                    d.push(data[$(this).attr("name").substr(4, 1)]);
                }
            });
            if (d.length > 0) {
                if (plot2) {
                    plot2.setData(d);
                    plot2.draw();
                } else {
                    plot2 = $.plot(target, d, options);
                }
            }

        }
    }

    function displayDashBoardAttemptsChart() {
        var ajaxData = {};
        ajaxData['action']      = 'retrieveInitialDashboardData';
        ajaxData['ajaxRequestNonce'] = GdbcAdmin.ajaxRequestNonce;
        $.ajax({
            type : "post",
            cache: false,
            dataType : "json",
            url : GdbcAdmin.ajaxUrl,
            data : ajaxData,
            success: function(response){
               	               
                $.each(response, function(prop, chartData){
                    if (prop === 'ChartDataArray' && $("#chart-container").length) {
                        var chartArray = [];
                        var attemptsCounter = 0;
                        $.each(chartData, function(key, value){
                            chartArray.push([parseInt(key), value]);
                            attemptsCounter += parseInt(value);
                        });
                        displayAttemptsChart("#chart-container", chartArray);
                        if(attemptsCounter > 9) {
                            $("a.btn-rate-gdbc").css('display', 'inline-block');
                        }
                    }
                });
            }
        });
    }

    function displayAttemptsChart(placeHolderId, data) {

        console.log(data);

       // var data = [[1304301600*1000, 20], [1304388000*1000, 30], [1304474400*1000, 40], [1304553600*1000, 10], [1304640000*1000, 23], [1304726400*1000, 16] ];


        var options = {
            xaxis : {
                mode : "time",
                tickLength : 5
            },
            yaxis : {
                mode : "number",
                tickFormatter: suffixFormatter
            },
            series : {
                lines : {
                    show : true,
                    lineWidth : 1,
                    fill : true,
                    fillColor : {
                        colors : [{
                            opacity : 0.1
                        }, {
                            opacity : 0.15
                        }]
                    }
                },
                shadowSize : 0,
                points : { show: false, fill: true }
            },
            selection : {
                mode : "x"
            },
            grid : {
                hoverable : true,
                clickable : true,
                tickColor : "#efefef",
                borderWidth : 0,
                borderColor : "#efefef"
            },
            tooltip:{
                show:true,
                content : '<div id = "flotTip"><p>%y attempts </p><p><b>%x</b></p>',
                xDateFormat: "%b %d, %Y",
                shifts: {
                    x: 10,
                    y: 10
                },
                defaultTheme: false
            },
            //tooltip : true,
            //tooltipOpts : {
            //    content : "<span>%y</span> attempts on <b>%x</b>",
            //    dateFormat : "%b %0d, %y",
            //    defaultTheme : false
            //},
            colors : ["#6595b4"]
        };

        $.plot($(placeHolderId), [data], options);
    }

    function displayLatestAttemptsTable()
    {
        if ($('#latest-attempts-table').length == 0)
            return;

        var ajaxData = {};
        ajaxData['action']      = 'retrieveLatestAttemptsTable';
        ajaxData['ajaxRequestNonce'] = GdbcAdmin.ajaxRequestNonce;
        var dataHeaderArr = null;
        $.ajax({
            type : "post",
            cache: false,
            dataType : "json",
            url : GdbcAdmin.ajaxUrl,
            data : ajaxData,
            success: function(response){
                
                if(response.success !== true)
                    return;

                $('#latest-attempts-table thead tr').empty();

                $.each(response.data.TableHeader, function (key, header) {
                    var cell = $('<th>' + header + '</th>');
                    $('#latest-attempts-table thead tr').append(cell);
                });

                $('#latest-attempts-table thead tr').append($('<th></th>'));

                $('#latest-attempts-table tbody').empty();
                $.each(response.data.TableData, function (index, tableData) {
                    var row = $('<tr></tr>');
                    var clientIp = 'N/A';
                    $.each(response.data.TableHeader, function (key, header) {

                        if(typeof tableData[key] === 'undefined')
                            tableData[key] = 'N/A';

                        if(key === 'ClientIp') {
                            clientIp = tableData[key];

                            if(tableData.IsIpBlocked) {
                                tableData[key] = '<i class="glyphicon glyphicon-minus-sign icon-danger" style="margin-right: 5px"></i>' + tableData[key];
                            }
                            else{
                                tableData[key] = '<i class="glyphicon glyphicon-eye-open" style="margin-right: 5px"></i>' + tableData[key];
                            }
                        }
                        var cell = $('<td>' + tableData[key] + '</td>');
                        row.append(cell);
                    });

                    var blockCell = $('<td></td>');
                    blockCell.append(createBlockIpButtonGroup(clientIp, !tableData.IsIpBlocked));
                    row.append(blockCell);

                    $('#latest-attempts-table tbody').append(row);

                });

            }
        });
    }

    function displayLocationsOnMap()
    {
        if ($('#vector-map').length == 0)
            return;

        $('#vector-map').vectorMap({
            map : 'world_mill_en',
            backgroundColor : '#fff',
            regionStyle : {
                initial : {
                    fill : '#c4c4c4'
                },
                hover : {
                    "fill-opacity" : 1
                }
            },
            series : {
                regions : [{
                    values : attemptsCountryArray,
                    scale : ['#85a8b6', '#4d7686'],
                    normalizeFunction : 'polynomial'
                }]
            },

            onRegionLabelShow : function(e, el, code) {
                if ( typeof attemptsCountryArray[code] == 'undefined') {
                    e.preventDefault();
                } else {
                    var countryLbl = attemptsCountryArray[code];
                    var attemptLbl = ' total attempts';
                    if (countryLbl == 1)
                        attemptLbl = ' attempt';
                    el.html(el.html() + ': ' + countryLbl + attemptLbl);
                }
            }
        });
    }


    function displayTotalAttemptsPerModule()
    {
        if ($('#gdbc-pie-chart').length == 0)
            return;

        var ajaxData = {};
        ajaxData['action']       = 'retrieveTotalAttemptsPerModule';
        ajaxData['ajaxRequestNonce'] = GdbcAdmin.ajaxRequestNonce;
        $.ajax({
            type : "post",
            cache: false,
            dataType : "json",
            url : GdbcAdmin.ajaxUrl,
            data : ajaxData,
            success: function(response) {
            	            	
                $.each(response, function(prop, moduleData) {
                    if ('TopAttemptsArrayPerModule' == prop) {
                        $('#gdbc-pie-chart table tbody').empty();
                        if (moduleData == 0)
                            $('#gdbc-pie-chart table tbody').append($('<tr><td colspan="4" class="text-center">No records found</tr>'));
                        else {

                            $.each(moduleData, function (k, v) {
                                var tableRow = $('<tr></tr>');
                                tableRow.append($('<td>' + v.label + '</td>'));
                                tableRow.append($('<td>' + v.value + '</td>'));
                                $('#gdbc-pie-chart table tbody').append(tableRow);
                            });

                            if ($('#gdbc-stats').length == 0)
                                return;

                            Morris.Donut({
                                element: 'gdbc-stats',
                                data: moduleData,
                                formatter: function (x, data) {
                                    return data.percent + "%"
                                }
                            });

                        }
                    }
                });
            }
        });
    }


    function createBlockIpButtonGroup(ip, shouldBlock)
    {
        var group = $('<div class="btn-group display-inline pull-right text-align-left hidden-tablet"></div>');
        group.append(createBlockLinkButton());
        group.append(createBlockLinkOptions(ip, shouldBlock));
        return group;
    }

    function createBlockLinkButton()
    {
        return $('<button data-toggle="dropdown" class="btn btn-xs btn-default dropdown-toggle">' +
        '<i class="glyphicon glyphicon-remove-circle icon-primary"></i>' +
        '</button>');
    }


    function manageIp(ip, shouldBlock, clickedElement)
    {
        var ajaxData = {};
        ajaxData['action']         = 'manageClientIpAddress';
        ajaxData['clientIp']       = ip;
        ajaxData['shouldBlock']    = shouldBlock;
        ajaxData['ajaxRequestNonce']   = GdbcAdmin.ajaxRequestNonce;
        jQuery.ajax({
            type : "post",
            cache: false,
            dataType : "json",
            url : GdbcAdmin.ajaxUrl,
            data : ajaxData,
            success: function(response) {
                if(!response.success){
                    return;
                }

                updatePageIpIcons(ip, shouldBlock);

                var cell = clickedElement.closest('td');
                cell.empty();
                cell.append(createBlockIpButtonGroup(ip, (shouldBlock == 0) ? 1 : 0));
            }
        });

    }

    function createBlockLinkOptions(ip, shouldBlock)
    {
        var blockIpLink = '<a href="javascript:void(0);" class="block-' +ip+ '"> <i class="glyphicon glyphicon-remove icon-danger" style = "margin-right: 5px"></i>Block Ip Address</a>';
        var unblockIpLink = '<a href="javascript:void(0);" class="unblock-' +ip+ '"> <i class="glyphicon glyphicon-ok icon-success" style = "margin-right: 5px"></i>UnBlock Ip Address</a>';

        var insertLink = '<li> ' + (shouldBlock ? blockIpLink : unblockIpLink) + ' <li>';

        var blockMenu = $('<ul class="dropdown-menu dropdown-menu-xs pull-right manage-ip-action-holder">' + insertLink +
        '<li class="divider"><li>' +
        '<li> <a href="javascript:void(0);">Cancel</a> <li>' +
        '</ul>');

        blockMenu.on('click', 'a', function(){
            var classAttr = $(this).attr('class');
            if (!classAttr)
                return void(0);

            if (classAttr.lastIndexOf('block-') === 0) {
                var ip = classAttr.substr(6);
                manageIp(ip, 1, $(this));

            } else if (classAttr.lastIndexOf('unblock-') === 0) {
                var ip = classAttr.substr(8);
                manageIp(ip, 0, $(this));
            }

        });

        return blockMenu;
    }



    function updatePageIpIcons(ip, shouldBlock)
    {
        $.each($("table i.glyphicon:first-child"), function(){
            var cellIp = $.trim($(this).parent().text());
            if (cellIp == ip){
                if (shouldBlock) {
                    $(this).attr("class", "glyphicon glyphicon-minus-sign icon-danger");
                }
                else {
                    $(this).attr("class", "glyphicon glyphicon-eye-open");
                }
            }
        });
    }

    // Util Functions Section
    function suffixFormatter(val)
    {
        return Math.round(val);
    }


    $('#gdbc-modal-holder').on('show.bs.modal', function (event) {
        
        $(".modal-body").hide();
        $(".modal-dialog").append("<div class = 'report-spinner'></div>");
        
        var clickedButton = $(event.relatedTarget)
        var attemptId = clickedButton.data('attempt');
        var modalDialog = $(this);

        var ajaxData = {};
        ajaxData['action']           = 'retrieveFormattedBlockedContent';
        ajaxData['attemptId']        = attemptId;
        ajaxData['ajaxRequestNonce'] = GdbcAdmin.ajaxRequestNonce;

        modalDialog.find('.modal-body').load(  GdbcAdmin.ajaxUrl, ajaxData, function() {
            $('div').removeClass('report-spinner');
            $(".modal-body").show();
        });
    })

    //$('#gdbc-modal-holder').on('hidden.bs.modal', function () {
    //    $(this).find('.modal-body').empty();
    //});

});