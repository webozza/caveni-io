(function ($) {
  "use strict";

  /**
   * All of the code for your public-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  jQuery(document).ready(function () {
    initGraphs();
    // Handling the toggle button for each filter container
    jQuery(".caveni-toggle-button").each(function (index) {
      jQuery(this).on("click", function () {
        // Get the position of the button (top and left)
        const buttonOffset = jQuery(this).offset();

        // Close all other filters first
        jQuery(".caveni-chart-filters")
          .not(jQuery(this).next())
          .stop()
          .slideUp(300);

        // Position the filter container relative to the button
        const filterContainer = jQuery(this).next(".caveni-chart-filters");
        const containerTop = buttonOffset.top + jQuery(this).outerHeight() + 5; // 5px margin below button
        const containerLeft = buttonOffset.left;

        // Set the new position dynamically
        filterContainer.css({
          top: "50px",
          left: "0px",
        });

        // Toggle the visibility of the clicked filter container
        filterContainer.stop().slideToggle(300);
      });
    });
  });

  function getCurrencySymbol(currencyCode) {
    const currencySymbols = {
      USD: "$",
      EUR: "€",
      GBP: "£",
      AUD: "A$",
      CAD: "C$",
      NZD: "NZ$",
      JPY: "¥",
      CNY: "¥",
      INR: "₹",
      RUB: "₽",
      BRL: "R$",
      ZAR: "R",
      CHF: "CHF",
      SEK: "kr",
      NOK: "kr",
      DKK: "kr",
      PLN: "zł",
      MXN: "Mex$",
      HKD: "HK$",
      SGD: "S$",
      THB: "฿",
      MYR: "RM",
      IDR: "Rp",
      PHP: "₱",
      VND: "₫",
      KRW: "₩",
      TRY: "₺",
      HUF: "Ft",
      CZK: "Kč",
      ILS: "₪",
      SAR: "﷼",
      AED: "د.إ",
      CLP: "CLP$",
      ARS: "ARS$",
      COP: "COP$",
      PEN: "S/",
      TWD: "NT$",
      PKR: "₨",
    };

    return currencySymbols[currencyCode] || currencyCode; // Return symbol or fallback to code
  }

  let accCurrency;

  let resetGraphs = () => {
    // Impressions Graph
    $("#clicks-chart-ppc").remove();
    $("#caveni__clicks-chart").append('<div id="clicks-chart-ppc"></div>');

    // Users Graph
    $("#conversions-chart-ppc").remove();
    $("#caveni__conversions-chart").append(
      '<div id="conversions-chart-ppc"></div>'
    );
  };

  function initGraphs() {
    resetGraphs();

    $("#top-metrics-ppc .loader-container").show();
    $(".caveni-table-reponsive .loader-container").show();

    $(
      "#campaign_body, #ad_group_body, #ads_body, .caveni-total-clicks.caveni-box-main-metric, .caveni-total-conversions.caveni-box-main-metric"
    ).empty();
    $("#clicks-chart-ppc, #conversions-chart-ppc").hide();
    $(".caveni-meter-chart").hide();

    let isCustomDateActivePPC =
      $('input[name="custom_date_selected"]').val() == "1";
    let startDatePPC;
    let endDatePPC;

    if (isCustomDateActivePPC) {
      startDatePPC = $('input[name="caveni_start_date"]').val();
      endDatePPC = $('input[name="caveni_end_date"]').val();
    } else {
      startDatePPC = $(
        ".seo-date-filters .caveni--tab-option.caveni--active"
      ).data("value");
      endDatePPC = "yesterday";
    }

    // $(".ci-loader").show();
    $(".caveni-seo-section.caveni-ppc .caveni-loader").show();
    let msgel = $(this);
    let caveniClientId = $(".client-search-seo").find(":selected").val();

    $.ajax({
      type: "POST",
      url: caveniPpc.ajaxurl,
      data: {
        action: "caveni_get_ppc_data",
        caveni_nonce: caveniPpc.security_get_data,
        caveni_client_id: caveniClientId,
        caveni_start_date: startDatePPC,
        caveni_end_date: endDatePPC,
      },
      success: function (response) {
        let timeZone = response.data.ga4_property_details.timeZone;

        $(
          ".caveni-seo-section.caveni-ppc .caveni-box-main-metric, .caveni-filter-container"
        ).removeClass("force-hide");
        $(".caveni-seo-section.caveni-ppc .caveni-loader").hide();
        if (response.success) {
          console.log(response.data);

          accCurrency = getCurrencySymbol(
            response.data.ga4_property_details.currency
          );

          renderAdClicksChart(
            response.data.dates,
            response.data.metric_total_ad_clicks,
            response.data.total_ad_cost,
            response.data.total_ad_impressions,
            response.data.ga4_property_details.googleAdsLinked
          );

          renderConversionsChart(
            response.data.dates,
            response.data.metric_total_conversions,
            response.data.ga4_property_details.googleAdsLinked
          );

          renderCampaignData(
            response.data.campaign_data,
            response.data.ga4_property_details.googleAdsLinked
          );
          renderAdGroupData(
            response.data.adgroup_data,
            response.data.ga4_property_details.googleAdsLinked
          );
          renderKeywordData(
            response.data.google_ads_keywords,
            response.data.ga4_property_details.googleAdsLinked
          );

          renderCostMeter(
            response.data.total_ad_cost,
            "cost-chart",
            response.data.ga4_property_details.googleAdsLinked
          );
        }

        $("#top-metrics-ppc .loader-container").hide();
        $(".caveni-meter-chart").show();
        $(".caveni-table-reponsive .loader-container").hide();

        setTimeout(() => {
          $("#clicks-chart-ppc, #conversions-chart-ppc").show();

          $("#campaign_body tr, #ad_group_body tr, #ads_body tr").each(
            function (index) {
              if (index >= 99) {
                $(this).remove();
              }
            }
          );
        }, 100);
      },
    });
  }

  // Highchart Meters
  let renderCPCMeter = ($total_cpc, $id, googleAdsLinked, $total_ad_cost) => {
    if (googleAdsLinked == false) {
      $(`#cpc-chart`).before(
        `<div class="caveni-error-handler"><p><b style="color: #f7284a">Error:</b> Google Ads is not linked to this GA4 property. Please connect a Google Ads account to view <b>CPC</b> data.</p></div>`
      );
      return;
    } else {
      if ($total_ad_cost == 0) {
        $(`#cpc-chart`).before(
          `<div class="caveni-error-handler"><p>N/A</b></div>`
        );
        return;
      }
    }

    // Define dynamic min and max based on CPC
    let minCPC = Math.floor($total_cpc * 0.1);
    let maxCPC = Math.ceil($total_cpc * 1.5);

    Highcharts.chart($id, {
      chart: {
        type: "gauge",
        plotBackgroundColor: null,
        plotBorderWidth: 0,
        plotShadow: false,
        height: "100%",
        backgroundColor: "transparent",
        spacingTop: 30,
        spacingBottom: 0,
      },
      title: {
        text: `<span style="font-size:30px; font-weight:600; color:#48465b;">${accCurrency}${$total_cpc.toFixed(
          2
        )}</span>`,
      },
      exporting: {
        enabled: false,
      },
      credits: {
        enabled: false,
      },
      pane: {
        startAngle: -90,
        endAngle: 90,
        background: {
          backgroundColor: "transparent",
          borderWidth: 0,
          borderColor: "transparent",
        },
        size: "130%",
      },
      yAxis: {
        min: minCPC,
        max: maxCPC,
        tickPosition: "inside",
        tickLength: 0,
        tickWidth: 0,
        minorTickLength: 0,
        minorTickWidth: 0,
        labels: {
          enabled: true,
          style: {
            color: "#48465b",
            fontSize: "13px",
            fontWeight: "500",
          },
          distance: -20,
          y: 16,
          formatter: function () {
            return `${accCurrency}${this.value.toFixed(2)}`;
          },
        },
        lineWidth: 0,
        plotBands: [
          {
            from: minCPC,
            to: maxCPC,
            color: "rgba(93, 102, 240, 0.05)",
            thickness: 35,
          },
          {
            from: minCPC,
            to: $total_cpc,
            color: {
              linearGradient: { x1: 0, x2: 1, y1: 0, y2: 0 },
              stops: [
                [0, "#3a6ff1"], // Lighter blue
                [1, "#5d66f0"], // Darker blue
              ],
            },
            thickness: 30,
          },
        ],
      },
      series: [
        {
          name: "CPC",
          color: "#FFFFFF",
          data: [
            {
              y: $total_cpc,
              name: "Cost Per Click",
            },
          ],
          dataLabels: {
            enabled: true,
            style: {
              fontSize: "14px",
              fontWeight: "500",
              color: "#48465b",
            },
            formatter: function () {
              return `${accCurrency}${this.y.toFixed(2)}`;
            },
          },
          tooltip: {
            enabled: false,
          },
          dial: {
            backgroundColor: "#FFFFFF",
            borderColor: "#5d66f0",
            borderWidth: 1,
            radius: "105%",
            baseWidth: 8,
            baseLength: "10%",
            rearLength: "0%",
          },
          pivot: {
            backgroundColor: "white",
            borderColor: "#5d66f0",
            borderWidth: 2,
            radius: 6,
          },
        },
      ],
    });
  };

  let renderCPMMeter = ($cpm, $id, googleAdsLinked, $total_ad_cost) => {
    if (googleAdsLinked == false) {
      $(`#cpm-chart`).before(
        `<div class="caveni-error-handler"><p><b style="color: #f7284a">Error:</b> Google Ads is not linked to this GA4 property. Please connect a Google Ads account to view <b>CPM</b> data.</p></div>`
      );
      return;
    } else {
      if ($total_ad_cost == 0) {
        $(`#cpm-chart`).before(
          `<div class="caveni-error-handler"><p>N/A</b></div>`
        );
        return;
      }
    }

    // Define dynamic min and max based on CPM
    let minCPM = 0;
    let maxCPM = Math.ceil($cpm * 1.5); // Scale dynamically

    Highcharts.chart($id, {
      chart: {
        type: "gauge",
        plotBackgroundColor: null,
        plotBorderWidth: 0,
        plotShadow: false,
        height: "100%",
        backgroundColor: "transparent",
        spacingTop: 30,
        spacingBottom: 0,
      },
      title: {
        text: `<span style="font-size:30px; font-weight:600; color:#48465b;">${accCurrency}${$cpm.toFixed(
          2
        )}</span>`,
      },
      exporting: {
        enabled: false,
      },
      credits: {
        enabled: false,
      },
      pane: {
        startAngle: -90,
        endAngle: 90,
        background: {
          backgroundColor: "transparent",
          borderWidth: 0,
          borderColor: "transparent",
        },
        size: "130%",
      },
      yAxis: {
        min: minCPM,
        max: maxCPM,
        tickPosition: "inside",
        tickLength: 0,
        tickWidth: 0,
        minorTickLength: 0,
        minorTickWidth: 0,
        labels: {
          enabled: true,
          style: {
            color: "#48465b",
            fontSize: "13px",
            fontWeight: "500",
          },
          distance: -20,
          y: 16,
          formatter: function () {
            return `${accCurrency}${this.value.toFixed(2)}`;
          },
        },
        lineWidth: 0,
        plotBands: [
          {
            from: minCPM,
            to: maxCPM,
            color: "rgba(93, 102, 240, 0.05)",
            thickness: 35,
          },
          {
            from: minCPM,
            to: $cpm,
            color: {
              linearGradient: { x1: 0, x2: 1, y1: 0, y2: 0 },
              stops: [
                [0, "#3a6ff1"], // Lighter blue
                [1, "#5d66f0"], // Darker blue
              ],
            },
            thickness: 30,
          },
        ],
      },
      series: [
        {
          name: "CPM",
          color: "#FFFFFF",
          data: [
            {
              y: $cpm,
              name: "CPM",
            },
          ],
          dataLabels: {
            enabled: true,
            style: {
              fontSize: "14px",
              fontWeight: "500",
              color: "#48465b",
            },
            formatter: function () {
              return `${accCurrency}${this.y.toFixed(2)}`;
            },
          },
          tooltip: {
            enabled: false,
          },
          dial: {
            backgroundColor: "#FFFFFF",
            borderColor: "#5d66f0",
            borderWidth: 1,
            radius: "105%",
            baseWidth: 8,
            baseLength: "10%",
            rearLength: "0%",
          },
          pivot: {
            backgroundColor: "white",
            borderColor: "#5d66f0",
            borderWidth: 2,
            radius: 6,
          },
        },
      ],
    });
  };

  let renderCTRMeter = ($ctr, $id, googleAdsLinked, $total_ad_cost) => {
    if (googleAdsLinked == false) {
      $(`#ctr-chart`).before(
        `<div class="caveni-error-handler"><p><b style="color: #f7284a">Error:</b> Google Ads is not linked to this GA4 property. Please connect a Google Ads account to view <b>CTR</b> data.</p></div>`
      );
      return;
    } else {
      if ($total_ad_cost == 0) {
        $(`#ctr-chart`).before(
          `<div class="caveni-error-handler"><p>N/A</b></div>`
        );
        return;
      }
    }

    if (!$id || !$(`#${$id}`).length) {
      console.error(`Error: Chart container with ID '${$id}' not found.`);
      return;
    }

    if ($ctr == NaN) {
      $ctr = 0;
    }

    // Define dynamic min and max based on CTR
    let minCTR = 0;
    let maxCTR = Math.ceil($ctr * 1.5); // Scale up dynamically

    Highcharts.chart($id, {
      chart: {
        type: "gauge",
        plotBackgroundColor: null,
        plotBorderWidth: 0,
        plotShadow: false,
        height: "100%",
        backgroundColor: "transparent",
        spacingTop: 30,
        spacingBottom: 0,
      },
      title: {
        text: `<span style="font-size:30px; font-weight:600; color:#48465b;">${$ctr.toFixed(
          1
        )}%</span>`,
      },
      exporting: {
        enabled: false,
      },
      credits: {
        enabled: false,
      },
      pane: {
        startAngle: -90,
        endAngle: 90,
        background: {
          backgroundColor: "transparent",
          borderWidth: 0,
          borderColor: "transparent",
        },
        size: "130%",
      },
      yAxis: {
        min: minCTR,
        max: maxCTR,
        tickPosition: "inside",
        tickLength: 0,
        tickWidth: 0,
        minorTickLength: 0,
        minorTickWidth: 0,
        labels: {
          enabled: true,
          style: {
            color: "#48465b",
            fontSize: "13px",
            fontWeight: "500",
          },
          distance: -20,
          y: 16,
          formatter: function () {
            return `${this.value.toFixed(1)}%`;
          },
        },
        lineWidth: 0,
        plotBands: [
          {
            from: minCTR,
            to: maxCTR,
            color: "rgba(93, 102, 240, 0.05)",
            thickness: 35,
          },
          {
            from: minCTR,
            to: $ctr,
            color: {
              linearGradient: { x1: 0, x2: 1, y1: 0, y2: 0 },
              stops: [
                [0, "#3a6ff1"], // Lighter blue
                [1, "#5d66f0"], // Darker blue
              ],
            },
            thickness: 30,
          },
        ],
      },
      series: [
        {
          name: "CTR",
          color: "#FFFFFF",
          data: [
            {
              y: $ctr,
              name: "CTR",
            },
          ],
          dataLabels: {
            enabled: true,
            style: {
              fontSize: "14px",
              fontWeight: "500",
              color: "#48465b",
            },
            formatter: function () {
              return `${this.y.toFixed(1)}%`;
            },
          },
          tooltip: {
            enabled: false,
          },
          dial: {
            backgroundColor: "#FFFFFF",
            borderColor: "#5d66f0",
            borderWidth: 1,
            radius: "105%",
            baseWidth: 8,
            baseLength: "10%",
            rearLength: "0%",
          },
          pivot: {
            backgroundColor: "white",
            borderColor: "#5d66f0",
            borderWidth: 2,
            radius: 6,
          },
        },
      ],
    });
  };

  let renderCostMeter = ($total_ad_cost, $id, googleAdsLinked) => {
    if (googleAdsLinked == false) {
      $(`#cost-chart`).before(
        `<div class="caveni-error-handler"><p><b style="color: #f7284a">Error:</b> Google Ads is not linked to this GA4 property. Please connect a Google Ads account to view <b>COST</b> data.</p></div>`
      );
      return;
    } else {
      if ($total_ad_cost == 0) {
        $(`#cost-chart`).before(
          `<div class="caveni-error-handler"><p>No spend reported for this period: <b>${accCurrency}${$total_ad_cost}</b></div>`
        );
        return;
      }
    }

    // Define dynamic min and max based on total cost
    let minCost = Math.floor($total_ad_cost * 0.1);
    let maxCost = Math.ceil($total_ad_cost * 1.5);

    let formattedTotalAdCost = $total_ad_cost.toLocaleString(undefined, {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });

    Highcharts.chart($id, {
      chart: {
        type: "gauge",
        plotBackgroundColor: null,
        plotBorderWidth: 0,
        plotShadow: false,
        height: "100%",
        backgroundColor: "transparent",
        spacingTop: 30,
        spacingBottom: 0,
      },
      title: {
        text: `<span style="font-size:30px; font-weight:600; color:#48465b;">${accCurrency}${formattedTotalAdCost}</span>`,
      },
      exporting: {
        enabled: false,
      },
      credits: {
        enabled: false,
      },
      pane: {
        startAngle: -90,
        endAngle: 90,
        background: {
          backgroundColor: "transparent",
          borderWidth: 0,
          borderColor: "transparent",
        },
        size: "130%",
      },
      yAxis: {
        min: minCost,
        max: maxCost,
        tickPosition: "inside",
        tickLength: 0,
        tickWidth: 0,
        minorTickLength: 0,
        minorTickWidth: 0,
        labels: {
          enabled: true,
          style: {
            color: "#48465b",
            fontSize: "13px",
            fontWeight: "500",
          },
          distance: -20,
          y: 16,
          formatter: function () {
            return `${accCurrency}${this.value.toFixed(2)}`;
          },
        },
        lineWidth: 0,
        plotBands: [
          {
            from: minCost,
            to: maxCost,
            color: "rgba(93, 102, 240, 0.05)",
            thickness: 35,
          },
          {
            from: minCost,
            to: $total_ad_cost,
            color: {
              linearGradient: { x1: 0, x2: 1, y1: 0, y2: 0 },
              stops: [
                [0, "#3a6ff1"], // Lighter blue
                [1, "#5d66f0"], // Darker blue
              ],
            },
            thickness: 30,
          },
        ],
      },
      series: [
        {
          name: "CPC",
          color: "#FFFFFF",
          data: [
            {
              y: $total_ad_cost,
              name: "Total Cost",
            },
          ],
          // tooltip: {
          //   useHTML: true,
          //   backgroundColor: "rgba(0,0,0,0.85)",
          //   borderWidth: 0,
          //   shadow: false,
          //   pointFormatter: function () {
          //     // Using `pointFormatter` instead of `formatter`
          //     let currencySymbol = accCurrency || "$"; // Default to $
          //     let currentValue = this.y;
          //     let previousValue = this.previousValue || this.y * 0.85;
          //     let currentDate = this.currentDate || "Jan 2 - 31";
          //     let previousDate = this.previousDate || "Jan 1 - 31";

          //     return `
          //           <div style="background-color:rgba(0,0,0,0.85); padding: 12px; border-radius: 8px; border: none; box-shadow: none; color: #fff;">
          //               <div style="font-size: 14px; font-weight: bold; color: #ffffff;">Avg. CPC / ${currencySymbol}</div>
          //               <div style="font-size: 12px; color: #cccccc; margin-top: 2px;">${currentDate}</div>
          //               <div style="font-size: 24px; font-weight: bold; color: #ffffff; margin: 8px 0;">
          //                   ${currencySymbol}${currentValue.toFixed(2)}
          //               </div>
          //               <div style="font-size: 14px; color: #bbbbbb;">
          //                   ${currencySymbol}${previousValue.toFixed(
          //       2
          //     )} on ${previousDate}
          //               </div>
          //           </div>
          //       `;
          //   },
          // },
          tooltip: {
            enabled: false,
          },
          dataLabels: {
            enabled: true,
            style: {
              fontSize: "14px",
              fontWeight: "500",
              color: "#48465b",
            },
            formatter: function () {
              return `${accCurrency}${this.y.toFixed(2)}`;
            },
          },
          dial: {
            backgroundColor: "#FFFFFF",
            borderColor: "#5d66f0",
            borderWidth: 1,
            radius: "105%",
            baseWidth: 8,
            baseLength: "10%",
            rearLength: "0%",
          },
          pivot: {
            backgroundColor: "white",
            borderColor: "#5d66f0",
            borderWidth: 2,
            radius: 6,
          },
        },
      ],
    });
  };

  // Apex Charts
  function renderAdClicksChart(
    dates,
    adClicks,
    totalAdCost,
    totalImpressions,
    googleAdsLinked
  ) {
    if (dates.length === 0 || adClicks.length === 0) {
      console.error("Insufficient data for chart rendering");
      return;
    }

    const totalDays = dates.length;
    const comparisonPeriodDays = Math.floor(totalDays / 2);
    const currentPeriodDays = totalDays - comparisonPeriodDays;

    const currentAdClicks = adClicks.slice(-currentPeriodDays);
    const previousAdClicks = adClicks.slice(-totalDays, -currentPeriodDays);
    const currentDates = dates.slice(-currentPeriodDays);
    const previousDates = dates.slice(-totalDays, -currentPeriodDays);

    const previousDatesMap = {};
    currentDates.forEach((date, index) => {
      previousDatesMap[date] = previousDates[index];
    });

    const allValues = [...currentAdClicks, ...previousAdClicks];
    const minValue = Math.min(...allValues);
    const dynamicMin = Math.max(minValue - 500, 1000);

    const currentTotal = currentAdClicks.reduce((sum, value) => sum + value, 0);

    const previousTotal = previousAdClicks.reduce(
      (sum, value) => sum + value,
      0
    );
    const percentageChange = previousTotal
      ? ((currentTotal - previousTotal) / previousTotal) * 100
      : 0;

    const isIncrease = percentageChange >= 0;
    const indicatorClass = isIncrease
      ? "caveni-indicator-up"
      : "caveni-indicator-down";
    const indicatorSVG = isIncrease ? increaseIndicator : decreaseIndicator;

    const formattedTotal = currentTotal.toLocaleString();
    const formattedPercentage = `${Math.abs(percentageChange).toFixed(1)}%`;

    let totalCPC = totalAdCost / currentTotal;
    renderCPCMeter(totalCPC, "cpc-chart", googleAdsLinked, totalAdCost);

    let totalCTR = (currentTotal / totalImpressions) * 100;
    renderCTRMeter(totalCTR, "ctr-chart", googleAdsLinked, totalAdCost);

    let totalCPM = (totalAdCost / totalImpressions) * 1000;
    renderCPMMeter(totalCPM, "cpm-chart", googleAdsLinked, totalAdCost);

    if (googleAdsLinked == false) {
      $(".caveni-total-clicks").append(`
          <div class="caveni-error-handler"><p><b style="color: #f7284a">Error:</b> Google Ads is not linked to this GA4 property. Please connect a Google Ads account to view <b>Clicks</b> data.</p></div>`);
      return;
    }

    $(".caveni-total-clicks").empty().append(`
        <div class="caveni-value">${formattedTotal}</div>
        <div class="caveni-indicator ${indicatorClass}">
            ${indicatorSVG} ${formattedPercentage}
        </div>
      `);

    const options = {
      chart: {
        type: "area",
        height: 300,
        toolbar: { show: false },
        background: "#ffffff",
      },
      stroke: { curve: "smooth", width: [3, 0] },
      colors: ["#5D66F0", "rgba(93, 102, 240, 0.3)"],
      series: [
        { name: "Current Conversions", data: currentAdClicks },
        { name: "Previous Conversions", data: previousAdClicks },
      ],
      fill: {
        type: "gradient",
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.5,
          opacityTo: 0.5,
          stops: [0, 100],
        },
      },
      xaxis: {
        categories: currentDates,
        tickAmount: 10,
        labels: {
          rotate: -45,
          // style: {
          //   fontSize: "13px",
          //   fontWeight: "500",
          //   color: "#48465b",
          // },
        },
        axisBorder: { show: false },
        axisTicks: { show: false },
      },
      tooltip: {
        shared: true,
        intersect: false,
        theme: "light",
        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
          const currentDate = currentDates[dataPointIndex] || "N/A";
          const previousDate = previousDates[dataPointIndex] || "N/A";

          const currentValue = series[0][dataPointIndex] || 0;
          const previousValue =
            dataPointIndex < series[1].length ? series[1][dataPointIndex] : 0;

          let difference = previousValue
            ? ((currentValue - previousValue) / previousValue) * 100
            : 0;

          if (previousValue == 0 && currentValue > 0) {
            difference = `${currentValue}00`;
          }

          if (currentValue == 0 && previousValue > 0) {
            difference = `-${previousValue}00`;
          }

          return `
                <div style="background-color:rgba(255,255,255,0.0001); padding: 12px; border-radius: 5px; border-color: transparent; border: none; box-shadow: none">
                  <div style="font-size: 14px; font-weight: bold; color: #333;">${currentDate}</div>
                  <div style="font-size: 24px; font-weight: bold; color: #333; margin: 8px 0;">
                    ${currentValue.toLocaleString()}
                  </div>
                  <div style="font-size: 12px; margin-bottom: 8px;">
                    ${
                      difference >= 0
                        ? `<span class="caveni-indicator caveni-indicator-up"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                              <polyline points="3 17 9 11 13 15 21 7"></polyline>
                              <path d="M19 7h2v2"></path>
                            </svg> ${Math.abs(difference).toFixed(1)}%</span>`
                        : `<span class="caveni-indicator caveni-indicator-down"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                              <polyline points="3 7 9 13 13 9 21 17"></polyline>
                              <path d="M19 17h2v-2"></path>
                            </svg> ${Math.abs(difference).toFixed(1)}%</span>`
                    }
                  </div>
                  <div style="display:none" class="prev-period-text">vs. previous period</div>
                  <div style="font-size: 14px; color: #555;">
                    ${previousValue.toLocaleString()} on ${previousDate}
                  </div>
                </div>
              `;
        },
      },
      grid: { borderColor: "#f0f0f0", strokeDashArray: 0 },
      legend: { show: false },
      dataLabels: { enabled: false },
      plotOptions: {
        area: {
          states: {
            hover: {
              filter: { type: "lighten", value: 0.2 },
            },
          },
        },
      },
      markers: {
        size: 0,
        strokeColor: "rgba(93, 102, 240, 0.2)",
        strokeWidth: 10,
        fillColor: "#5D66F0",
        hover: {
          size: 6,
          strokeColor: "#5D66F0",
          strokeWidth: 10,
        },
      },
    };

    const chartElement = document.querySelector("#clicks-chart-ppc");

    if (chartElement) {
      const chart = new ApexCharts(chartElement, options);
      // chart.destroy();
      // $("#clicks-chart-ppc").attr("style", "");
      // $("#clicks-chart-ppc").empty();

      chart.render();
    } else {
      console.error("Chart container not found");
    }
  }

  function renderConversionsChart(dates, conversions, googleAdsLinked) {
    if (dates.length === 0 || conversions.length === 0) {
      console.error("Insufficient data for chart rendering");
      return;
    }

    // Calculate the range dynamically based on the number of dates available
    const totalDays = dates.length;
    const comparisonPeriodDays = Math.floor(totalDays / 2); // Half for previous period comparison
    const currentPeriodDays = totalDays - comparisonPeriodDays;

    // Slice data dynamically based on the calculated ranges
    const currentConversions = conversions.slice(-currentPeriodDays);
    const previousConversions = conversions.slice(
      -totalDays,
      -currentPeriodDays
    );
    const currentDates = dates.slice(-currentPeriodDays);
    const previousDates = dates.slice(-totalDays, -currentPeriodDays);

    // Create a mapping of currentDates to previousDates
    const previousDatesMap = {};
    currentDates.forEach((date, index) => {
      previousDatesMap[date] = previousDates[index];
    });

    // Calculate y-axis dynamic min value
    const allValues = [...currentConversions, ...previousConversions];
    const minValue = Math.min(...allValues);
    const dynamicMin = Math.max(minValue - 500, 1000);

    // Totals and percentage change
    const currentTotal = currentConversions.reduce(
      (sum, value) => sum + value,
      0
    );

    if (googleAdsLinked == false) {
      $(".caveni-total-conversions").append(`
          <div class="caveni-error-handler"><p><b style="color: #f7284a">Error:</b> Google Ads is not linked to this GA4 property. Please connect a Google Ads account to view <b>Conversions</b> data.</p></div>`);
      return;
    }

    const previousTotal = previousConversions.reduce(
      (sum, value) => sum + value,
      0
    );
    const percentageChange = previousTotal
      ? ((currentTotal - previousTotal) / previousTotal) * 100
      : 0;

    // Update total conversions display
    const isIncrease = percentageChange >= 0;
    const indicatorClass = isIncrease
      ? "caveni-indicator-up"
      : "caveni-indicator-down";
    const indicatorSVG = isIncrease ? increaseIndicator : decreaseIndicator;

    const formattedTotal = currentTotal.toLocaleString();
    const formattedPercentage = `${Math.abs(percentageChange).toFixed(1)}%`;

    $(".caveni-total-conversions").empty().append(`
        <div class="caveni-value">${formattedTotal}</div>
        <div class="caveni-indicator ${indicatorClass}">
            ${indicatorSVG} ${formattedPercentage}
        </div>
      `);

    // Chart options
    const options = {
      chart: {
        type: "area",
        height: 300,
        toolbar: { show: false },
        background: "#ffffff",
      },
      stroke: { curve: "smooth", width: [3, 0] },
      colors: ["#5D66F0", "rgba(93, 102, 240, 0.3)"],
      series: [
        { name: "Current Conversions", data: currentConversions },
        { name: "Previous Conversions", data: previousConversions },
      ],
      fill: {
        type: "gradient",
        gradient: {
          shadeIntensity: 1,
          opacityFrom: 0.5,
          opacityTo: 0.5,
          stops: [0, 100],
        },
      },
      xaxis: {
        categories: currentDates,
        tickAmount: 10,
        labels: {
          rotate: -45,
          // style: {
          //   fontSize: "13px",
          //   fontWeight: "500",
          //   color: "#48465b",
          // },
        },
        axisBorder: { show: false },
        axisTicks: { show: false },
      },
      tooltip: {
        shared: true,
        intersect: false,
        theme: "light",
        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
          const currentDate = currentDates[dataPointIndex] || "N/A";
          const previousDate = previousDates[dataPointIndex] || "N/A";

          const currentValue = series[0][dataPointIndex] || 0;
          const previousValue =
            dataPointIndex < series[1].length ? series[1][dataPointIndex] : 0;

          let difference = previousValue
            ? ((currentValue - previousValue) / previousValue) * 100
            : 0;

          if (previousValue == 0 && currentValue > 0) {
            difference = `${currentValue}00`;
          }

          if (currentValue == 0 && previousValue > 0) {
            difference = `-${previousValue}00`;
          }

          return `
                    <div style="background-color:rgba(255,255,255,0.0001); padding: 12px; border-radius: 5px; border-color: transparent; border: none; box-shadow: none">
                      <div style="font-size: 14px; font-weight: bold; color: #333;">${currentDate}</div>
                      <div style="font-size: 24px; font-weight: bold; color: #333; margin: 8px 0;">
                        ${currentValue.toLocaleString()}
                      </div>
                      <div style="font-size: 12px; margin-bottom: 8px;">
                        ${
                          difference >= 0
                            ? `<span class="caveni-indicator caveni-indicator-up">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                              <polyline points="3 17 9 11 13 15 21 7"></polyline>
                              <path d="M19 7h2v2"></path>
                            </svg> ${Math.abs(difference).toFixed(1)}%</span>`
                            : `<span class="caveni-indicator caveni-indicator-down"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                              <polyline points="3 7 9 13 13 9 21 17"></polyline>
                              <path d="M19 17h2v-2"></path>
                            </svg> ${Math.abs(difference).toFixed(1)}%</span>`
                        }
                      </div>
                      <div style="display:none" class="prev-period-text">vs. previous period</div>
                      <div style="font-size: 14px; color: #555;">
                        ${previousValue.toLocaleString()} on ${previousDate}
                      </div>
                    </div>
                  `;
        },
      },
      grid: { borderColor: "#f0f0f0", strokeDashArray: 0 },
      legend: { show: false },
      dataLabels: { enabled: false },
      plotOptions: {
        area: {
          states: {
            hover: {
              filter: { type: "lighten", value: 0.2 },
            },
          },
        },
      },
      markers: {
        size: 0,
        strokeColor: "rgba(93, 102, 240, 0.2)",
        strokeWidth: 10,
        fillColor: "#5D66F0",
        hover: {
          size: 6,
          strokeColor: "#5D66F0",
          strokeWidth: 10,
        },
      },
    };

    // Render chart
    const chartElement = document.querySelector("#conversions-chart-ppc");
    if (chartElement) {
      const chart = new ApexCharts(chartElement, options);
      chart.render();
    } else {
      console.error("Chart container not found");
    }
  }

  // Overviews
  function renderCampaignData(campaignData, googleAdsLinked) {
    if (googleAdsLinked == false) {
      $(".caveni-campaigns-overview .caveni-table-reponsive").empty().append(`
        <div class="caveni-error-handler"><p><b style="color: #f7284a">Error:</b> Google Ads is not linked to this GA4 property. Please connect a Google Ads account to view <b>Campaign</b> data.</p></div>
      `);
      return;
    } else {
      if ($.isArray(campaignData) && campaignData.length === 0) {
        $(".caveni-campaigns-overview .caveni-table-reponsive").empty().append(`
          <div class="caveni-error-handler"><p>No active campaigns found for this period.</p></div>
        `);
        return;
      }
    }

    let campaignHtml = "";

    $.each(campaignData, function (index, campaign) {
      let formattedCost = campaign.cost.toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      });

      formattedCost = `${accCurrency}${formattedCost}`;

      campaignHtml +=
        "<tr><td>" +
        (index + 1) +
        "</td><td>" +
        campaign.campaign +
        "</td><td>" +
        campaign.impressions.toLocaleString() +
        "</td><td> " +
        formattedCost +
        "</td></tr>";
    });

    $("#campaign_body").html(campaignHtml);
  }

  function renderAdGroupData(adGroupData, googleAdsLinked) {
    if (googleAdsLinked == false) {
      $(".caveni-adgroups-overview .caveni-table-reponsive").empty().append(`
        <div class="caveni-error-handler"><p><b style="color: #f7284a">Error:</b> Google Ads is not linked to this GA4 property. Please connect a Google Ads account to view <b>Ad Groups</b> data.</p></div>
      `);
    } else {
      if ($.isArray(adGroupData) && adGroupData.length === 0) {
        $(".caveni-adgroups-overview .caveni-table-reponsive").empty().append(`
          <div class="caveni-error-handler"><p>No active ad groups found for this period.</p></div>
        `);
        return;
      }
    }

    let adGroupHtml = "";

    $.each(adGroupData, function (index, adGroup) {
      let formattedCost = adGroup.cost.toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      });

      formattedCost = `${accCurrency}${formattedCost}`;

      adGroupHtml +=
        "<tr><td>" +
        (index + 1) +
        "</td><td>" +
        adGroup.ad_group +
        "</td><td>" +
        adGroup.impressions.toLocaleString() +
        "</td><td> " +
        formattedCost +
        "</td></tr>";
    });

    $("#ad_group_body").html(adGroupHtml);
  }

  function renderKeywordData(keywords, googleAdsLinked) {
    if (googleAdsLinked == false) {
      $(".caveni-keywords-overview .caveni-table-reponsive").empty().append(`
        <div class="caveni-error-handler"><p><b style="color: #f7284a">Error:</b> Google Ads is not linked to this GA4 property. Please connect a Google Ads account to view <b>Keywords</b> data.</p></div>
      `);
    } else {
      if ($.isArray(keywords) && keywords.length === 0) {
        $(".caveni-keywords-overview .caveni-table-reponsive").empty().append(`
          <div class="caveni-error-handler"><p>No active keywords found for this period.</p></div>
        `);
        return;
      }
    }

    let keywordHtml = "";

    $.each(keywords, function (index, keyword) {
      let formattedCost = keyword.cost.toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      });

      formattedCost = `${accCurrency}${formattedCost}`;

      keywordHtml +=
        "<tr><td>" +
        (index + 1) +
        "</td><td>" +
        keyword.keyword +
        "</td><td>" +
        keyword.impressions.toLocaleString() +
        "</td><td> " +
        formattedCost +
        "</td></tr>";
    });

    $("#ads_body").html(keywordHtml);
  }

  $(".seo-client-search-form").submit(async function (e) {
    e.preventDefault();
    initGraphs();
  });
})(jQuery);
