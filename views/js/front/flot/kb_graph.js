/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store. 
 *
 * @category  PrestaShop Module
 * @author    knowband.com <support@knowband.com>
 * @copyright 2016 knowband
 * @license   see file: LICENSE.txt
 */

var kb_seller_graph_title = '';

$(document).ready(function(){
    $('select[name="sales_statistics_type"]').trigger('change');
});

function displaySalesGraph(e)
{
    kb_seller_graph_title = $(e).find('option:selected').html();
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: kb_current_request + ((kb_current_request.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true'
            +'&graph_type='+$(e).val(),
        beforeSend: function() {
            $('#kb_seller_sales_graph_container .loader128').show();
            $('#kb_seller_sales_graph_container .kb_graph_area').hide();
            $('#kb_seller_sales_graph_container .kb_graph_area .kb_graph_legend_container').html('');
            $('#kb_seller_sales_graph_container .kb_graph_area .kb_graph_container').html('');
        },
        success: function(json)
        {
            if (json.length){
                $('#kb_seller_sales_graph_container .loader128').hide();
                $('#kb_seller_sales_graph_container .kb_graph_area').show();
                generateSellerDashboardGraph(json);
            }else{
                $('#kb_seller_sales_graph_container .loader128').hide();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            jAlert(kb_ajax_request_fail_err);
        }
    });
}


function generateSellerDashboardGraph(dataObj) {
    var ticks = [], total_revenue = [], seller_revenue = [], admin_revenue = [], total_orders = [], total_qty = [];
    graph_tooltip_data = [];
    for (var i = 0; i < dataObj.length; i++) {
        var tooltip_row = [];
        tooltip_row.push(['Total Revenue', dataObj[i]['formatted_total_revenue']]);
        tooltip_row.push(['Your Revenue', dataObj[i]['formatted_seller_revenue']]);
        tooltip_row.push(['Admin Revenue', dataObj[i]['formatted_admin_revenue']]);
        tooltip_row.push(['Total Orders', parseInt(dataObj[i]['total_order'])]);
        tooltip_row.push(['Product Sold', parseInt(dataObj[i]['ordered_qty'])]);
        graph_tooltip_data.push(tooltip_row);
        ticks.push([i, dataObj[i]['xaxis']]);
        total_orders.push([i, parseInt(dataObj[i]['total_order'])]);
        total_revenue.push([i, dataObj[i]['total_revenue']]);
        seller_revenue.push([i, dataObj[i]['seller_revenue']]);
        admin_revenue.push([i, dataObj[i]['admin_revenue']]);
        total_qty.push([i, dataObj[i]['ordered_qty']]);
    }

    var dataset = [
        {
            label: kb_graph_revenue_label,
            data: seller_revenue,
            yaxis: 1,
            bars: {order: 1, lineWidth: 0}
        },
        {
            label: kb_graph_orders_label,
            data: total_orders,
            yaxis: 2,
            bars: {order: 2, lineWidth: 0}
        }, {
            label: kb_graph_products_label,
            data: total_qty,
            yaxis: 3,
            bars: {order: 3, lineWidth: 0}
        }
    ];

    var options = {
        bars: {
            show: true,
            barWidth: 0.2,
            fill: 1
        },
        series: {
            grow: {active: false}
        },
        xaxis: {ticks: ticks,autoscaleMargin: 0.01,axisLabel: kb_seller_graph_title,rotateTicks: 145},
        yaxes: [{
                min: 0,
                position: "left",
                color: "black",
                axisLabel: kb_graph_revenue_label,
                axisLabelUseCanvas: true,
                axisLabelFontSizePixels: 12,
                axisLabelFontFamily: 'Verdana, Arial',
                axisLabelPadding: 20
            }, {
                min: 0,
                position: "right",
                color: "black",
                axisLabel: kb_graph_orders_label,
                axisLabelUseCanvas: true,
                axisLabelFontSizePixels: 12,
                axisLabelFontFamily: 'Verdana, Arial',
                axisLabelPadding: 3,
                tickDecimals: 0
            }, {
                min: 0,
                alignTicksWithAxis: 2,
                position: "right",
                color: "black",
                axisLabel: kb_graph_products_label,
                axisLabelUseCanvas: true,
                axisLabelFontSizePixels: 12,
                axisLabelFontFamily: 'Verdana, Arial',
                axisLabelPadding: 3,
                tickDecimals: 0
            }
        ],
        legend: {
            noColumns: 0,
            backgroundColor: 'null',
            backgroundOpacity: 0.9,
            labelBoxBorderColor: '#000000',
            container: jQuery('#kb_graph_legend_holder'),
            position: "ne"
        },
        grid: {
            hoverable: true,
            borderWidth: 1,
            borderColor: '#EEEEEE',
            mouseActiveRadius: 10,
            backgroundColor: "#ffffff",
            axisMargin: 20
        }
//        tooltip: true,
//            tooltipOpts: {
//            content: "%s : %y",
//            shifts: {x: -30, y: -50},
//            defaultTheme: false
//        }
    };

    previousPoint = null;
    previousLabel = null;
    jQuery.plot(jQuery("#kb_seller_sales_graph"), dataset, options);
    jQuery("#kb_seller_sales_graph").CreateVerticalGraphToolTip();
    
    window.onresize = function(event) {
        jQuery.plot(jQuery("#kb_seller_sales_graph"), dataset, options);
    }
}

jQuery.fn.CreateVerticalGraphToolTip = function() {
    jQuery(this).bind("plothover", function(event, pos, item) {
        if (item) {
            if ((previousLabel != item.series.label) || (previousPoint != item.dataIndex)) {
                previousPoint = item.dataIndex;
                previousLabel = item.series.label;
                jQuery("#tooltip").remove();

                var x = item.datapoint[0];
                var y = item.datapoint[1];

                var color = item.series.color;
                showCustomTooltip(previousPoint, item.pageX, item.pageY, color,
                        "<strong>" + item.series.label + "</strong>" +
                        " : <strong>" + y + "</strong> ");
            }
        } else {
            jQuery("#tooltip").remove();
            previousPoint = null;
        }
    });
};

function showCustomTooltip(dataIndex, x, y, color)
{
    var html = '';
    var data_array = graph_tooltip_data[dataIndex];

    for (var i = 0; i < data_array.length; i++) {
        html += '<p class="bva_graph_tooltip"><strong>' + data_array[i][0] + ': </strong>' + data_array[i][1] + '</p>';
    }
    jQuery('<div id="tooltip">' + html + '</div>').css({
        position: 'absolute',
        display: 'none',
        top: y - 40,
        left: x - 120,
        border: '2px solid ' + color,
        padding: '5px',
        'font-size': '9px',
        'border-radius': '5px',
        'background-color': '#fff',
        'font-family': 'Verdana, Arial, Helvetica, Tahoma, sans-serif',
        opacity: 0.9
    }).appendTo("body").fadeIn(200);
}