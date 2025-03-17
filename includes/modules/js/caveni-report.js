(function ($) {
  "use strict";

  function renderUsersChart(cats, userClicks) {
    if (cats.length === 0 || userClicks.length === 0) {
      console.error("Insufficient data for chart rendering");
      return;
    }

    // Calculate the range dynamically based on the number of dates available
    const totalDays = cats.length;
    const comparisonPeriodDays = Math.floor(totalDays / 2); // Half for previous period comparison
    const currentPeriodDays = totalDays - comparisonPeriodDays;

    // Slice data dynamically based on the calculated ranges
    const currentUsers = userClicks.slice(-currentPeriodDays);
    const previousUsers = userClicks.slice(-totalDays, -currentPeriodDays);
    const currentDates = cats.slice(-currentPeriodDays);
    const previousDates = cats.slice(-totalDays, -currentPeriodDays);

    // Create a mapping of currentDates to previousDates
    const previousDatesMap = {};
    currentDates.forEach((date, index) => {
      previousDatesMap[date] = previousDates[index];
    });

    // Calculate y-axis dynamic min value
    const allValues = [...currentUsers, ...previousUsers];
    const minValue = Math.min(...allValues);
    const dynamicMin = Math.max(minValue - 500, 1000);

    // Totals and percentage change
    const currentTotal = currentUsers.reduce((sum, value) => sum + value, 0);
    const previousTotal = previousUsers.reduce((sum, value) => sum + value, 0);
    const percentageChange = previousTotal
      ? ((currentTotal - previousTotal) / previousTotal) * 100
      : 0;

    // Update total users display
    const isIncrease = percentageChange >= 0;
    const indicatorClass = isIncrease
      ? "caveni-indicator-up"
      : "caveni-indicator-down";
    const indicatorSVG = isIncrease ? increaseIndicator : decreaseIndicator;

    const formattedTotal = currentTotal.toLocaleString();
    const formattedPercentage = `${Math.abs(percentageChange).toFixed(1)}%`;

    $(".caveni-total-users").empty().append(`
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
        { name: "Current Users", data: currentUsers },
        { name: "Previous Users", data: previousUsers },
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
                          </svg> ${Math.abs(difference).toFixed(1)}%
                        </span>`
                    : `<span class="caveni-indicator caveni-indicator-down">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 7 9 13 13 9 21 17"></polyline>
                            <path d="M19 17h2v-2"></path>
                          </svg> ${Math.abs(difference).toFixed(1)}%
                        </span>`
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
    const chartElement = document.querySelector("#user-chart-seo");
    if (chartElement) {
      const chart = new ApexCharts(chartElement, options);

      // chart.destroy();
      // $("#user-chart-seo").attr("style", "");
      // $("#user-chart-seo").empty();

      chart.render();
    } else {
      console.error("Chart container not found");
    }
  }

  function renderImpressionChart(categories, impressions) {
    if (impressions.length === 0 || categories.length === 0) {
      console.error("Insufficient data for chart rendering");
      return;
    }

    // Calculate the range dynamically based on the number of dates available
    const totalDays = categories.length;
    const comparisonPeriodDays = Math.floor(totalDays / 2); // Half for previous period comparison
    const currentPeriodDays = totalDays - comparisonPeriodDays;

    // Slice data dynamically based on the calculated ranges
    const currentImpressions = impressions.slice(-currentPeriodDays);
    const previousImpressions = impressions.slice(
      -totalDays,
      -currentPeriodDays
    );
    const currentDates = categories.slice(-currentPeriodDays);
    const previousDates = categories.slice(-totalDays, -currentPeriodDays);

    // Map current dates to previous dates for the tooltip
    const previousDatesMap = {};
    currentDates.forEach((date, index) => {
      previousDatesMap[date] = previousDates[index];
    });

    // Calculate y-axis dynamic min value
    const allValues = [...currentImpressions, ...previousImpressions];
    const minValue = Math.min(...allValues);
    const dynamicMin = Math.max(minValue - 500, 0); // Ensure minimum of 0

    // Totals and percentage change
    const currentTotal = currentImpressions.reduce(
      (sum, value) => sum + value,
      0
    );
    const previousTotal = previousImpressions.reduce(
      (sum, value) => sum + value,
      0
    );
    const percentageChange = previousTotal
      ? ((currentTotal - previousTotal) / previousTotal) * 100
      : 0;

    // Update total impressions display
    const isIncrease = percentageChange >= 0;
    const indicatorClass = isIncrease
      ? "caveni-indicator-up"
      : "caveni-indicator-down";
    const indicatorSVG = isIncrease ? increaseIndicator : decreaseIndicator;

    const formattedTotal = currentTotal.toLocaleString();
    const formattedPercentage = `${Math.abs(percentageChange).toFixed(1)}%`;

    $(".caveni-total-impressions").empty().append(`
      <div class="caveni-value">${formattedTotal}</div>
      <div class="caveni-indicator ${indicatorClass}">
          ${indicatorSVG} ${formattedPercentage}
      </div>
    `);

    // Totals and percentage change for Impressions
    const currentImpressionsTotal = currentImpressions.reduce(
      (sum, value) => sum + value,
      0
    );
    const previousImpressionsTotal = previousImpressions.reduce(
      (sum, value) => sum + value,
      0
    );
    const impressionsPercentageChange = previousImpressionsTotal
      ? ((currentImpressionsTotal - previousImpressionsTotal) /
          previousImpressionsTotal) *
        100
      : 0;

    // Update the Impressions display
    const formattedImpressionsTotal = currentImpressionsTotal.toLocaleString();
    const formattedImpressionsPercentage = `${Math.abs(
      impressionsPercentageChange
    ).toFixed(1)}%`;
    const impressionsIndicatorClass =
      impressionsPercentageChange >= 0
        ? "caveni-indicator-up"
        : "caveni-indicator-down";
    const impressionsIndicatorSVG =
      impressionsPercentageChange >= 0 ? increaseIndicator : decreaseIndicator;

    $(".caveni-impressions").empty().append(`
  <div class="caveni-value">${formattedImpressionsTotal}</div>
  <div class="caveni-indicator ${impressionsIndicatorClass}">
      ${impressionsIndicatorSVG} ${formattedImpressionsPercentage}
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
        { name: "Current Impressions", data: currentImpressions },
        { name: "Previous Impressions", data: previousImpressions },
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
        categories: currentDates, // Show only current dates on the x-axis
        tickAmount: Math.min(10, currentDates.length), // Limit to a readable number of ticks
        labels: {
          rotate: -45,
          // style: {
          //   fontSize: "13px",
          //   fontWeight: "500",
          //   color: "#48465b",
          // },
        },
        axisBorder: {
          show: false,
        },
        axisTicks: {
          show: false,
        },
      },
      yaxis: {
        min: 0, // Ensure the minimum value starts at 0
        labels: {
          formatter: function (value) {
            return Math.round(value); // Round to the nearest integer
          },
          // style: {
          //   fontSize: "13px",
          //   fontWeight: "500",
          //   color: "#48465b",
          // },
        },
        tickAmount: 5, // Ensure a reasonable number of ticks
      },

      tooltip: {
        shared: true,
        intersect: false,
        theme: "light",
        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
          const currentDate = currentDates[dataPointIndex] || "N/A"; // Current date
          const previousDate = previousDatesMap[currentDate] || "N/A"; // Previous date

          const currentValue = series[0][dataPointIndex] || 0; // Current Impressions
          const previousValue =
            dataPointIndex < series[1].length ? series[1][dataPointIndex] : 0; // Previous Impressions

          // Calculate percentage difference
          let difference = previousValue
            ? ((currentValue - previousValue) / previousValue) * 100
            : 0;

          if (previousValue == 0 && currentValue > 0) {
            difference = `${currentValue}00`;
          }

          if (currentValue == 0 && previousValue > 0) {
            difference = `-${previousValue}00`;
          }

          // Return the tooltip with proper date mapping
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
                          </svg> ${Math.abs(difference).toFixed(1)}%
                        </span>`
                    : `<span class="caveni-indicator caveni-indicator-down">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 7 9 13 13 9 21 17"></polyline>
                            <path d="M19 17h2v-2"></path>
                          </svg> ${Math.abs(difference).toFixed(1)}%
                        </span>`
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
      area: {
        states: {
          hover: {
            filter: { type: "lighten", value: 0.2 },
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
    const chartElement = document.querySelector("#impression-chart-seo");
    if (chartElement) {
      const chart = new ApexCharts(chartElement, options);
      //chart.destroy();
      chart.render();
    } else {
      console.error("Chart container not found");
    }
  }

  function renderKeywordData(keywords) {
    let keywordHtml = "";

    $.each(keywords, function (index, keyword) {
      keywordHtml +=
        "<tr><td>" +
        keyword.keyword +
        "</td><td>" +
        keyword.avg_position +
        "</td>";

      // Determine the indicator based on trend
      let indicatorHtml = "";
      if (keyword.trend === "pre_low") {
        // Decrease indicator
        indicatorHtml = `
          <td>
            <span class="caveni-indicator caveni-indicator-down">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 7 9 13 13 9 21 17"></polyline>
                <path d="M19 17h2v-2"></path>
              </svg> ${keyword.percentage_change}
            </span>
          </td>
        `;
      } else {
        // Increase indicator
        indicatorHtml = `
          <td>
            <span class="caveni-indicator caveni-indicator-up">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 17 9 11 13 15 21 7"></polyline>
                <path d="M19 7h2v2"></path>
              </svg> ${keyword.percentage_change}
            </span>
          </td>
        `;
      }

      // Append the indicator HTML to the row
      keywordHtml += indicatorHtml + "</tr>";
    });

    // Update the table body
    $("#avg_position_body").html(keywordHtml);
  }

  function renderKeywordImpressions(impressions) {
    let impressionsHtml = "";

    $.each(impressions, function (index, keyword) {
      impressionsHtml +=
        "<tr><td>" +
        keyword.keyword +
        "</td><td>" +
        keyword.impressions +
        "</td>";

      // Determine the indicator based on trend
      let indicatorHtml = "";
      if (keyword.trend === "pre_low") {
        // Decrease indicator
        indicatorHtml = `
          <td>
            <span class="caveni-indicator caveni-indicator-down">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 7 9 13 13 9 21 17"></polyline>
                <path d="M19 17h2v-2"></path>
              </svg> ${keyword.percentage_change}
            </span>
          </td>
        `;
      } else {
        // Increase indicator
        indicatorHtml = `
          <td>
            <span class="caveni-indicator caveni-indicator-up">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="3 17 9 11 13 15 21 7"></polyline>
                <path d="M19 7h2v2"></path>
              </svg> ${keyword.percentage_change}
            </span>
          </td>
        `;
      }

      // Append the indicator HTML to the row
      impressionsHtml += indicatorHtml + "</tr>";
    });

    // Update the table body for impressions
    $("#keyword_body").html(impressionsHtml);
  }

  function renderSourceData(source) {
    let sourceHtml = "";
    $.each(source, function (key, val) {
      // Start building the row with source and users
      sourceHtml += "<tr><td>" + val.source + "</td><td>" + val.users + "</td>";

      // Determine the indicator based on trend
      let indicatorHtml = "";
      if (val.trend === "pre_low") {
        // Decrease indicator
        indicatorHtml = `
              <td>
                <span class="caveni-indicator caveni-indicator-down">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 7 9 13 13 9 21 17"></polyline>
                    <path d="M19 17h2v-2"></path>
                  </svg> ${val.percentage_change}
                </span>
              </td>
            `;
      } else {
        // Increase indicator
        indicatorHtml = `
              <td>
                <span class="caveni-indicator caveni-indicator-up">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 17 9 11 13 15 21 7"></polyline>
                    <path d="M19 7h2v2"></path>
                  </svg> ${val.percentage_change}
                </span>
              </td>
            `;
      }

      // Append the indicator HTML to the row
      sourceHtml += indicatorHtml + "</tr>";
    });

    // Update the table body
    $("#source_body").html(sourceHtml);
  }

  function renderEngagementData(engagement) {
    let engagementHtml = engagement
      .map((val, key) => {
        let subtextClass = key === 0 || key === 3 ? "rate-down" : "";
        let rateClass = "caveni-indicator caveni-indicator-up"; // Default to increase
        let rateIcon = increaseIndicator; // Default to increase icon

        if (val[2] === "pre_low") {
          rateClass = "caveni-indicator caveni-indicator-down"; // Change to decrease
          rateIcon = decreaseIndicator; // Change to decrease icon
        }

        return `
        <div class="engagement-info">
          <div class="caveni-subtext">${val[0]}</div>
          <div class="caveni-value">${val[1]}</div>
          <div class="${rateClass}">
            ${rateIcon}${val[3]}
          </div>
        </div>
      `;
      })
      .join(""); // Combine all the HTML fragments into a single string

    $("#top-metrics").html(engagementHtml);
  }

  function renderTopMetrics(metrics) {
    // Define the icons array
    const icons = [
      "fe fe-user-plus",
      "fe fe-clock",
      "fe fe-eye",
      "fe fe-external-link",
    ];

    const metricsHtml = metrics
      .map(([title, value, trend, change, color], index) => {
        const rateClass =
          trend === "pre_low" ? "caveni-indicator-down" : "caveni-indicator-up";
        const rateIcon =
          trend === "pre_low" ? decreaseIndicator : increaseIndicator;

        // Get the correct icon based on index
        const iconClass = icons[index % icons.length];

        return `
                <div class="caveni-box">
                    <div class="caveni-box-header">
                        <div class="caveni-flex">
                            <div class="caveni-icon">
                                <i class="${iconClass}"></i>
                            </div>
                            <div class="caveni-title">${title}</div>
                        </div>
                        <div class="info-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                    </div>
                    <div class="caveni-box-content">
                        <div class="caveni-value">
                            ${value}
                            <span class="caveni-indicator ${rateClass}">
                                ${rateIcon} ${change}
                            </span>
                        </div>
                    </div>
                </div>
            `;
      })
      .join("");

    // Add the HTML to the container
    document.getElementById("top-metrics").innerHTML = metricsHtml;
  }

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

  function renderKeywordDataPPC(keywords, googleAdsLinked) {
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

  // Delete report
  $(".caveni-delete-report").on("click", function () {
    var reportId = $(this).data("report-id");
    if (!reportId) {
      alert("Invalid report ID.");
      return;
    }

    if (
      !confirm(
        "Are you sure you want to delete this report? This action cannot be undone."
      )
    ) {
      return;
    }

    $.ajax({
      url: ajaxurl, // WordPress AJAX URL
      type: "POST",
      data: {
        action: "caveni_delete_report",
        report_id: reportId,
        security: caveniReportsData.nonce, // Ensure security
      },
      beforeSend: function () {
        $(".caveni--overlay-loader").show();
      },
      success: function (response) {
        $(".caveni--overlay-loader").hide();
        if (response.success) {
          // alert(response.data.message);
          location.reload(); // Refresh page after successful delete
        } else {
          alert("Error: " + response.data.message);
        }
      },
      error: function () {
        $(".caveni--overlay-loader").hide();
        alert("Something went wrong. Please try again.");
      },
    });
  });

  // Open PDF Viewer
  $(".caveni-view-report").on("click", function () {
    var pdfUrl = $(this).data("report-url");
    if (!pdfUrl) {
      alert("No report URL found.");
      return;
    }

    // Show loading indicator
    $(".caveni--overlay-loader").show();

    // Set iframe src
    $("#pdfViewerFrame").attr("src", pdfUrl);

    // Show modal after slight delay (to allow iframe loading)
    setTimeout(function () {
      $(".caveni--overlay-loader").hide();
      $("#crm_pdf_viewer_modal").modal("show");
    }, 1000);
  });

  // Close PDF Viewer
  $("#closePdfViewer").on("click", function () {
    $("#pdfViewerModal").modal("hide");
    $("#pdfViewerFrame").attr("src", ""); // Reset iframe to stop loading
  });

  // Open the Modal
  $("#crm_report_add").on("click", function () {
    $("#crm_modal_title").text("Add New Report"); // Set modal title
    $(".error-message").remove(); // Clear previous errors
    $("#crm_addform")[0].reset(); // Reset the form
    $("#submissionType").val(""); // Reset hidden submissionType field
    $("#crm_newsmodal").modal("show"); // Show modal
  });

  // Close Modal
  $(document).on("click", ".btn-close-report", function () {
    $("#crm_newsmodal").modal("hide");
    $("body").removeClass("modal-open");
    $(".modal-backdrop").remove();
    $("#crm_pdf_viewer_modal").modal("hide");
    $("#pdfViewerFrame").attr("src", ""); // Reset iframe to stop loading
  });

  // Initialize Select2 and Period Picker When Modal is Opened
  $("#crm_newsmodal").on("shown.bs.modal", function () {
    $(".crm_select")
      .select2({
        dropdownParent: $("#crm_newsmodal"),
        placeholder: "Select Company",
        allowClear: true,
        width: "100%",
      })
      .val(null)
      .trigger("change"); // Ensure it's cleared when modal opens

    if (typeof $.fn.periodpicker !== "undefined") {
      $("#crm_date_range").periodpicker({
        end: "#crm_end_date",
        formatDate: "YYYY-MM-DD",
        cells: [1, 2],
        todayButton: false,
        onChangePeriod: function () {
          // This part can be simplified
          let startDate = $(".period_picker_selected").first().data("date");
          let endDate = $(".period_picker_selected").last().data("date");
        },
      });
    } else {
      console.log("Period Picker is not loaded.");
    }
  });

  // Function to convert DD-MM-YY to YYYY-MM-DD
  function formatDateString(dateStr) {
    if (!dateStr) return ""; // Handle empty string case

    // Ensure proper splitting
    var parts = dateStr.split("-");
    if (parts.length !== 3) return dateStr; // Return original if not in expected format

    var day = parts[0].padStart(2, "0"); // Ensure two digits
    var month = parts[1].padStart(2, "0");
    var year = "20" + parts[2]; // Convert YY to 20YY

    return `${year}-${month}-${day}`;
  }

  // Update hidden company name field when a company is selected
  $("#crm_company").on("change", function () {
    var selectedCompanyID = $(this).val(); // Get company ID
    var selectedCompanyName = $(this).find(":selected").data("company") || ""; // Get company name from data attribute
    $("#crm_company_name").val(selectedCompanyName); // Set the hidden input value
  });

  // Show the preloader on page load
  $(window).on("load", function () {
    $("#preloader").fadeOut("slow"); // Hide preloader once page is fully loaded
  });

  let caveniStartDate;
  let caveniEndDate;

  let disablebtns = () => {
    $(
      "#crm_addform .modal-footer button, #crm_newsmodal .btn-close-report"
    ).attr("disabled", true);
    $("#crm_addform").attr("style", "pointer-events: none");
  };

  let enablebtns = () => {
    $(
      "#crm_addform .modal-footer button, #crm_newsmodal .btn-close-report"
    ).removeAttr("disabled");
    $("#crm_addform").attr("style", "pointer-events: auto");
  };

  // Handle Form Submission (Redirect to JSON Output)
  $(".submission-btn").on("click", function (event) {
    event.preventDefault(); // Prevent default form submission

    // Show the preloader
    $("#preloader").show();

    var submissionType = $(this).val(); // Get button value
    $("#submissionType").val(submissionType); // Set hidden field

    // Ensure the latest selected company name is stored
    var selectedCompanyID = $("#crm_company").val();
    var selectedCompanyName =
      $("#crm_company").find(":selected").data("company") || "";
    $("#crm_company_name").val(selectedCompanyName);

    // Extract the correct start and end dates from the .period_picker_selected elements
    var startDate = $(".period_picker_selected").first().data("date") || "";
    var endDate = $(".period_picker_selected").last().data("date") || "";

    // Format extracted dates before storing them
    caveniStartDate = formatDateString(startDate);
    caveniEndDate = formatDateString(endDate);

    caveniStartDate = startDate;
    caveniEndDate = endDate;

    // Redirect to the JSON output and show preloader
    $("#preloader").show(); // Ensure the preloader is shown
  });

  $("#crm_newsmodal .submission-btn").click(async function () {
    disablebtns();

    let reportType = $(this).attr("value");
    let serviceSelected = $("#crm_service").find(":selected").val();
    let clientId = $("#crm_company").find(":selected").val();

    $(".caveni--overlay-loader h5").text(
      `Hold tight while we generate your Caveni ${serviceSelected} Report!`
    );
    $(".caveni--overlay-loader").show().css("display", "flex");

    // ✅ Fetch Report Data via AJAX
    $.ajax({
      url: caveniReportsData.ajax_url,
      method: "POST",
      data: {
        action: "caveni_fetch_seo_data",
        security: caveniReportsData.nonce,
        start_date: caveniStartDate,
        end_date: caveniEndDate,
        client: clientId,
        service: serviceSelected,
      },
      success: async function (response) {
        if (response.success) {
          if (serviceSelected == "SEO") {
            console.log("✅ SEO Data Received:", response.data);

            // ✅ Render Charts
            renderUsersChart(
              response.data.dates,
              response.data.metric_total_users
            );
            renderImpressionChart(
              response.data.dates,
              response.data.metric_total_impressions
            );
            renderKeywordData(response.data.average_position);
            renderKeywordImpressions(response.data.impression_data);
            renderSourceData(response.data.users_by_source);
            renderEngagementData(response.data.engagement);
            renderTopMetrics(response.data.engagement);

            setTimeout(async () => {
              let topMetricsImage = await captureSectionAsImage("#top-metrics");
              let caveniChartImageSEO = await captureSectionAsImage(
                ".caveni-seo-charts"
              );
              let avgPositionImage = await captureSectionAsImage(
                "#caveni__seo-report .caveni-container-row .caveni-box:first-child .caveni-box-title"
              );
              let impressionByKeywordImage = await captureSectionAsImage(
                "#caveni__seo-report .caveni-container-row .caveni-box:nth-child(2) .caveni-box-title"
              );
              let userBySourceImage = await captureSectionAsImage(
                "#caveni__seo-report .caveni-container-row .caveni-box:last-child .caveni-box-title"
              );

              let apiDataAvgPosition = JSON.stringify(
                response.data.average_position
              );

              let apiDataImpressionsByKeyword = JSON.stringify(
                response.data.impression_data
              );

              let apiDataUsersBySource = JSON.stringify(
                response.data.users_by_source
              );

              // ✅ Send Data to Generate PDF
              sendPDFRequest(
                reportType,
                serviceSelected,
                clientId,
                topMetricsImage,
                caveniChartImageSEO,
                avgPositionImage,
                impressionByKeywordImage,
                userBySourceImage,
                apiDataAvgPosition,
                apiDataImpressionsByKeyword,
                apiDataUsersBySource
              );
            }, 2000);
          } else if (serviceSelected == "PPC") {
            console.log("✅ PPC Data Received:", response.data);

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
            renderKeywordDataPPC(
              response.data.google_ads_keywords,
              response.data.ga4_property_details.googleAdsLinked
            );

            renderCostMeter(
              response.data.total_ad_cost,
              "cost-chart",
              response.data.ga4_property_details.googleAdsLinked
            );

            // ✅ Wait for Charts to Finish Rendering
            await waitForChartsToRender();

            // ✅ Capture Charts as Images
            console.log("✅ Capturing Charts as Images...");

            setTimeout(async () => {
              let topMetricsImage = await captureSectionAsImage(
                "#top-metrics-ppc"
              );
              let caveniChartImageSEO = await captureSectionAsImage(
                ".caveni-ppc-charts"
              );
              let avgPositionImage = await captureSectionAsImage(
                "#caveni__ppc-report .caveni-container-row .caveni-box:first-child .caveni-box-title"
              );
              let impressionByKeywordImage = await captureSectionAsImage(
                "#caveni__ppc-report .caveni-container-row .caveni-box:nth-child(2) .caveni-box-title"
              );
              let userBySourceImage = await captureSectionAsImage(
                "#caveni__ppc-report .caveni-container-row .caveni-box:last-child .caveni-box-title"
              );

              let apiDataCampaignOverview = JSON.stringify(
                response.data.campaign_data
              );

              let apiDataAdGroupsOverview = JSON.stringify(
                response.data.adgroup_data
              );

              let apiDataKeywordsOverview = JSON.stringify(
                response.data.google_ads_keywords
              );

              // ✅ Send Data to Generate PDF
              sendPDFRequest(
                reportType,
                serviceSelected,
                clientId,
                topMetricsImage,
                caveniChartImageSEO,
                avgPositionImage,
                impressionByKeywordImage,
                userBySourceImage,
                apiDataCampaignOverview,
                apiDataAdGroupsOverview,
                apiDataKeywordsOverview
              );
            }, 2000);
          }
        } else {
          console.error("❌ Error fetching SEO data:", response.data.message);
          alert("Error fetching SEO data: " + response.data.message);
          $(".caveni--overlay-loader").hide();
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error(
          "❌ AJAX Error (SEO Data Fetch):",
          textStatus,
          errorThrown
        );
        alert("Failed to fetch SEO data. Please try again.");
        $(".caveni--overlay-loader").hide();
      },
    });
  });

  async function waitForChartsToRender() {
    return new Promise((resolve) => {
      let retries = 0;
      let maxRetries = 10; // Adjust this if needed
      let checkInterval = 500; // 500ms per retry

      function checkCharts() {
        let userChart = document.querySelector("#caveni__user-chart canvas");
        let impressionChart = document.querySelector(
          "#caveni__impression-chart canvas"
        );

        if (userChart && impressionChart) {
          console.log("✅ Charts fully loaded, proceeding with capture.");
          resolve();
        } else if (retries < maxRetries) {
          retries++;
          console.log(`⏳ Waiting for charts to render... Attempt ${retries}`);
          setTimeout(checkCharts, checkInterval);
        } else {
          console.warn("⚠ Charts took too long to render, capturing anyway.");
          resolve();
        }
      }
      checkCharts();
    });
  }

  // ✅ Convert Any Section to an Image
  async function captureSectionAsImage(selector) {
    let element = document.querySelector(selector);
    if (!element) {
      console.error("❌ Section not found:", selector);
      return "";
    }

    try {
      let canvas = await html2canvas(element, {
        backgroundColor: null, // Transparent BG
        scale: 3, // High-Resolution
        useCORS: true, // Ensures external images load properly
      });

      return canvas.toDataURL("image/png"); // Convert to Base64
    } catch (error) {
      console.error("❌ Error capturing section:", error);
      return "";
    }
  }

  // ✅ Convert SVG Charts to Images Using `html2canvas`
  async function captureChartAsImage(chartSelector) {
    let chartElement = document.querySelector(chartSelector);
    if (!chartElement) {
      console.error("❌ Chart not found:", chartSelector);
      return "";
    }

    try {
      let canvas = await html2canvas(chartElement, {
        backgroundColor: null, // Transparent background
        scale: 2, // High quality
      });
      return canvas.toDataURL("image/png"); // Convert to Base64
    } catch (error) {
      console.error("❌ Error capturing chart:", error);
      return "";
    }
  }

  // ✅ Replace Chart HTML with Image
  function replaceChartWithImage(chartSelector, imageSrc) {
    let chartElement = document.querySelector(chartSelector);
    if (!chartElement || !imageSrc) return;

    let imgElement = document.createElement("img");
    imgElement.src = imageSrc;
    imgElement.style.maxWidth = "100%";

    // Replace chart with image
    chartElement.innerHTML = "";
    chartElement.appendChild(imgElement);
  }

  // ✅ Handle Fetch Errors for GA4/GSC
  function handleFetchErrors(fetchErrors) {
    if (!fetchErrors) return;

    if (fetchErrors.impressions) {
      $(".caveni-impressions.caveni-box-main-metric").hide();
      $(".caveni-impressions.caveni-box-main-metric").after(`
            <div class="caveni-error-handler">
                <p><b style="color: #f7284a">Error:</b> ${fetchErrors.impressions}</p>
            </div>
        `);
      $("#impression-chart-seo").remove();
    }

    if (fetchErrors.keywordPosition) {
      $(
        ".keyword-avg-position .caveni-table, .impressions-by-keyword .caveni-table"
      ).after(`
            <div class="caveni-error-handler">
                <p><b style="color: #f7284a">Error:</b> ${fetchErrors.keywordPosition}</p>
            </div>
        `);
    }
  }

  // ✅ 6️⃣ Function to Send the PDF Request
  function sendPDFRequest(
    reportType,
    serviceSelected,
    clientId,
    topMetricsImage,
    caveniChartImageSEO,
    avgPositionImage,
    impressionByKeywordImage,
    userBySourceImage,
    apiDataAvgPosition,
    apiDataImpressionsByKeyword,
    apiDataUsersBySource
  ) {
    let reportHtmlElement =
      serviceSelected === "SEO"
        ? $("#caveni__seo-report")
        : $("#caveni__ppc-report");

    if (!reportHtmlElement.length) {
      console.error("❌ Report element not found!");
      alert(
        "Error: Report content is missing. Please generate the report first."
      );
      $(".caveni--overlay-loader").hide();
      return;
    }

    let clonedReportElement = reportHtmlElement.clone(); // Clone to keep the original intact

    clonedReportElement.find("#top-metrics").remove();
    clonedReportElement.find("#caveni__user-chart").remove();
    clonedReportElement.find("#caveni__impression-chart").remove();

    let reportHtml = clonedReportElement.prop("outerHTML").trim();

    let minifiedHtml = reportHtml
      .replace(/\s{2,}/g, " ")
      .replace(/\n/g, "")
      .replace(/>\s+</g, "><")
      .replace(/<!--.*?-->/g, "")
      .replace(/\s?bis_skin_checked="1"/g, ""); // Remove bis_skin_checked="1"

    let encodedHtml = $("<div>").append(minifiedHtml).html();

    console.log(encodedHtml);
    $(".caveni--overlay-loader h5").text(
      `Your Caveni ${serviceSelected} Report is almost ready!`
    );

    let pdfData;

    if (serviceSelected == "SEO") {
      pdfData = {
        action: "caveni_generate_report",
        security: caveniReportsData.nonce,
        report_html: encodedHtml,
        report_type: reportType,
        start_date: caveniStartDate,
        end_date: caveniEndDate,
        client: clientId,
        service: serviceSelected,
        top_metrics_image: topMetricsImage,
        caveni_chart_image_seo: caveniChartImageSEO,
        avg_position_image: avgPositionImage,
        impression_by_keyword_image: impressionByKeywordImage,
        users_by_source_image: userBySourceImage,
        api_data_avg_position: apiDataAvgPosition,
        api_data_impressions_by_keyword: apiDataImpressionsByKeyword,
        api_data_users_by_source: apiDataUsersBySource,
      };
    }

    if (serviceSelected == "PPC") {
      pdfData = {
        action: "caveni_generate_report",
        security: caveniReportsData.nonce,
        report_html: encodedHtml,
        report_type: reportType,
        start_date: caveniStartDate,
        end_date: caveniEndDate,
        client: clientId,
        service: serviceSelected,
        top_metrics_image: topMetricsImage,
        caveni_chart_image_seo: caveniChartImageSEO,
        avg_position_image: avgPositionImage,
        impression_by_keyword_image: impressionByKeywordImage,
        users_by_source_image: userBySourceImage,
        api_data_campaign_overview: apiDataAvgPosition,
        api_data_ad_groups_overview: apiDataImpressionsByKeyword,
        api_data_keywords_overview: apiDataUsersBySource,
      };
    }

    // ✅ Second AJAX request - Generate PDF
    $.ajax({
      url: caveniReportsData.ajax_url,
      method: "POST",
      data: pdfData,
      success: function (pdfResponse) {
        console.log("✅ Full AJAX Response:", pdfResponse);

        if (pdfResponse.success) {
          console.log("✅ Report successfully generated!", pdfResponse.data);
          console.log("📄 PDF Report URL:", pdfResponse.data.report_url);

          // ✅ Automatically trigger file download
          if (reportType === "Download") {
            let reportUrl = pdfResponse.data.report_url;
            let filename = reportUrl.substring(reportUrl.lastIndexOf("/") + 1);

            let downloadLink = document.createElement("a");
            downloadLink.href = reportUrl;
            downloadLink.download = filename;

            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
          }

          // ✅ Automatically trigger file download
          if (reportType == "Send") {
            // Send report selected
          }
        } else {
          console.error("❌ Error generating PDF: " + pdfResponse.data.message);
          alert("Error: " + pdfResponse.data.message);
          enablebtns();
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error("❌ AJAX Error:", textStatus, errorThrown);
        alert("Failed to generate the report. Please try again.");
        enablebtns();
      },
      complete: function () {
        console.log("✅ PDF Report Process Complete");
        $(".caveni--overlay-loader").hide();

        // SEO Charts
        $("#impression-chart-seo").remove();
        $("#user-chart-seo").remove();

        $("#caveni__impression-chart").append(
          `<div id="impression-chart-seo"></div>`
        );
        $("#caveni__user-chart").append(`<div id="user-chart-seo"></div>`);

        // PPC Charts
        $("#clicks-chart-ppc").remove();
        $("#conversions-chart-ppc").remove();

        $("#caveni__clicks-chart").append(`<div id="clicks-chart-ppc"></div>`);
        $("#caveni__conversions-chart").append(
          `<div id="conversions-chart-ppc"></div>`
        );

        enablebtns();

        // ✅ Close modal after PDF generation
        $(".btn-close-report").click();

        if (reportType == "Send") {
          location.reload();
        }
      },
    });
  }
})(jQuery);
