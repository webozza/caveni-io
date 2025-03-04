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
    let currentUrl = window.location.href;

    if (
      currentUrl.indexOf("?elementor-preview") > -1 ||
      currentUrl.indexOf("post.php?") > -1
    ) {
      console.log("is admin area");
    } else {
      initGraphs();
    }

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
          top: "70px",
          right: "0",
        });

        // Toggle the visibility of the clicked filter container
        filterContainer.stop().slideToggle(300);
      });
    });
  });

  function formatDate(date) {
    let year = date.getFullYear();
    let month = String(date.getMonth() + 1).padStart(2, "0"); // Ensure 2-digit month
    let day = String(date.getDate()).padStart(2, "0"); // Ensure 2-digit day
    return `${year}-${month}-${day}`;
  }

  function caveniSorter() {
    $(".caveni-seo-section .caveni-table thead th").on("click", function () {
      let table = $(this).closest("table");
      let tbody = table.find("tbody");
      let rows = tbody.find("tr").toArray();
      let index = $(this).index();
      let isNumeric = index > 0; // Assume numeric sorting for columns after Dimension
      let isAscending = $(this).data("order") === "asc";

      rows.sort(function (rowA, rowB) {
        let cellA = $(rowA).find("td").eq(index).text().trim();
        let cellB = $(rowB).find("td").eq(index).text().trim();

        if (isNumeric) {
          // Convert to numbers (remove commas and $ signs if any)
          cellA = parseFloat(cellA.replace(/[^0-9.-]/g, "")) || 0;
          cellB = parseFloat(cellB.replace(/[^0-9.-]/g, "")) || 0;

          return isAscending ? cellA - cellB : cellB - cellA; // Sort numerically
        } else {
          return isAscending
            ? cellA.localeCompare(cellB)
            : cellB.localeCompare(cellA); // Sort alphabetically
        }
      });

      // Toggle sorting order
      $(this).data("order", isAscending ? "desc" : "asc");

      // Append sorted rows back to the table body
      tbody.append(rows);
    });
  }

  function initGraphs() {
    $("#impression-chart-seo, #user-chart-seo").hide();
    $(".caveni-error-handler").remove();
    $(".caveni-meter-chart").empty();

    $(
      ".caveni-impressions.caveni-box-main-metric, .caveni-total-users.caveni-box-main-metric, #keyword_body, #avg_position_body, #source_body"
    ).empty();

    $("#top-metrics .caveni-box-content .loader-container").remove();

    $("#top-metrics .caveni-box-content").prepend(`
      <div class="loader-container">
          <img decoding="async" src="https://caveni.local/wp-content/plugins/caveni-io//public/images/metrics-loader.gif" alt="Loading..." class="loader-gif">
      </div>  
    `);
    $("#top-metrics .caveni-value").remove();

    $(".caveni-table-reponsive .loader-container").show();

    let isCustomDateActive =
      $('input[name="custom_date_selected"]').val() == "1";
    let startDate;
    let endDate;

    if (isCustomDateActive) {
      startDate = $('input[name="caveni_start_date"]').val();
      endDate = $('input[name="caveni_end_date"]').val();
    } else {
      startDate = $(
        ".seo-date-filters .caveni--tab-option.caveni--active"
      ).data("value");
      endDate = "yesterday";
    }

    $(".caveni-seo-section .caveni-loader").show();
    let msgel = $(this);
    let caveniClientId = $(".client-search-seo").find(":selected").val();

    $.ajax({
      type: "POST",
      url: caveniSeo.ajaxurl,
      data: {
        action: "caveni_get_seo_data",
        caveni_nonce: caveniSeo.security_get_data,
        caveni_client_id: caveniClientId,
        caveni_start_date: startDate,
        caveni_end_date: endDate,
      },
      success: function (response) {
        let timeZone = response.data.ga4_property_details.timeZone;

        $(".caveni-chart-filters .filter-item").click(function () {
          $(".caveni--select-option option[value='custom']")
            .prop("disabled", false)
            .prop("selected", true);

          setTimeout(() => {
            $(".caveni--select-option option[value='custom']").prop(
              "disabled",
              true
            );
          }, 100);

          let selectedRange = $(this).text().trim();

          // Create a Date object in the correct timezone
          let now = new Date(new Date().toLocaleString("en-US", { timeZone }));

          console.log(`time now in ${timeZone}`, now);
          let mtd = new Date(now.getFullYear(), now.getMonth(), 1);
          console.log(mtd);

          let startDate, endDate;

          // Function to get the first Monday of the current week
          function getMonday(d) {
            d = new Date(d);
            let day = d.getDay();
            let diff = d.getDate() - (day === 0 ? 6 : day - 1); // Adjust for Monday start
            return new Date(d.setDate(diff));
          }

          switch (selectedRange) {
            // ✅ TODAY/YESTERDAY
            case "Today":
              startDate = endDate = now;
              break;
            case "Yesterday":
              startDate = endDate = new Date(now.setDate(now.getDate() - 1));
              break;

            // ✅ THIS/LAST WEEK
            case "This Week":
              startDate = getMonday(now);
              endDate = now;
              break;
            case "Last Week":
              startDate = getMonday(now);
              startDate.setDate(startDate.getDate() - 7);
              endDate = new Date(startDate);
              endDate.setDate(endDate.getDate() + 6);
              break;

            // ✅ WEEK TO DATE
            case "Week to Date":
              startDate = getMonday(now);
              endDate = now;
              break;

            // ✅ THIS/LAST MONTH
            case "This Month":
              startDate = new Date(now.getFullYear(), now.getMonth(), 1);
              endDate = now;
              break;
            case "Last Month":
              startDate = new Date(now.getFullYear(), now.getMonth() - 1, 1);
              endDate = new Date(now.getFullYear(), now.getMonth(), 0);
              break;

            // ✅ MONTH TO DATE
            case "Month to Date":
              startDate = new Date(now.getFullYear(), now.getMonth(), 1);
              endDate = now;
              break;

            // ✅ THIS/LAST QUARTER
            case "This Quarter":
              let quarterStartMonth = Math.floor(now.getMonth() / 3) * 3;
              startDate = new Date(now.getFullYear(), quarterStartMonth, 1);
              endDate = now;
              break;
            case "Last Quarter":
              let lastQuarterStartMonth =
                Math.floor(now.getMonth() / 3) * 3 - 3;
              startDate = new Date(now.getFullYear(), lastQuarterStartMonth, 1);
              endDate = new Date(
                now.getFullYear(),
                lastQuarterStartMonth + 3,
                0
              );
              break;

            // ✅ QUARTER TO DATE
            case "Quarter to Date":
              let qtdStartMonth = Math.floor(now.getMonth() / 3) * 3;
              startDate = new Date(now.getFullYear(), qtdStartMonth, 1);
              endDate = now;
              break;

            // ✅ THIS/LAST YEAR
            case "This Year":
              startDate = new Date(now.getFullYear(), 0, 1);
              endDate = now;
              break;
            case "Last Year":
              startDate = new Date(now.getFullYear() - 1, 0, 1);
              endDate = new Date(now.getFullYear() - 1, 11, 31);
              break;

            // ✅ YEAR TO DATE
            case "Year to Date":
              startDate = new Date(now.getFullYear(), 0, 1);
              endDate = now;
              break;

            default:
              console.log("Unknown range selected:", selectedRange);
              return;
          }

          // Format dates as YYYY-MM-DD
          let formattedStartDate = formatDate(startDate);
          let formattedEndDate = formatDate(endDate);

          console.log(
            "Start Date:",
            formattedStartDate,
            "End Date:",
            formattedEndDate,
            "Timezone:",
            timeZone
          );

          // Set values in hidden inputs
          $('input[name="caveni_start_date"]').val(formattedStartDate);
          $('input[name="caveni_end_date"]').val(formattedEndDate);

          // Mark custom date as selected
          $('input[name="custom_date_selected"]').val("1");

          // Submit form
          $(".seo-client-search-form").submit();

          $(".caveni-chart-filters").attr("style", "display:none");
        });

        $(
          ".caveni-seo-section .caveni-box-main-metric, .caveni-filter-container"
        ).removeClass("force-hide");
        $(".caveni-seo-section .caveni-loader").hide();

        if (response.success) {
          console.log(response.data);

          renderUsersChart(
            response.data.dates,
            response.data.metric_total_users
          );

          renderKeywordData(response.data.average_position);
          renderKeywordImpressions(response.data.impression_data);
          renderSourceData(response.data.users_by_source);
          renderEngagementData(response.data.engagement);
          renderTopMetrics(response.data.engagement);

          // GA4 not connect with GSC
          if (
            response.data.fetch_errors &&
            response.data.fetch_errors.impressions &&
            response.data.fetch_errors.impressions.length
          ) {
            $(".caveni-impressions.caveni-box-main-metric").css(
              "display",
              "none"
            );

            $(".caveni-impressions.caveni-box-main-metric").after(`
              <div class="caveni-error-handler">
                <p><b style="color: #f7284a">Error:</b> ${response.data.fetch_errors.impressions}</p>  
              </div>  
            `);

            $("#impression-chart-seo").remove();
          }

          // Failed to fetch average position for keywords
          // GA4 not connect with GSC
          if (
            response.data.fetch_errors &&
            response.data.fetch_errors.keywordPosition &&
            response.data.fetch_errors.keywordPosition.length
          ) {
            $(
              ".keyword-avg-position .caveni-table, .impressions-by-keyword .caveni-table"
            ).after(`
                <div class="caveni-error-handler">
                  <p><b style="color: #f7284a">Error:</b> ${response.data.fetch_errors.keywordPosition}</p>  
                </div>
            `);
          }

          renderImpressionChart(
            response.data.dates,
            response.data.metric_total_impressions
          );
        }

        setTimeout(() => {
          $("#impression-chart-seo, #user-chart-seo").show();
          $(".caveni-table-reponsive .loader-container").hide();

          $("#avg_position_body tr, #keyword_body tr, #source_body tr").each(
            function (index) {
              if (index >= 99) {
                $(this).remove();
              }
            }
          );

          caveniSorter();
        }, 100);
      },
    });
  }

  function generatePlotLines(data, step = 1000) {
    const maxValue = Math.max(...data); // Get the maximum value from the data
    const plotLines = [];
    for (let i = step; i <= Math.ceil(maxValue / step) * step; i += step) {
      plotLines.push({
        color: "rgba(0,0,0,0.1)", // Line color
        width: 1, // Line width
        value: i, // Position on Y-axis
        zIndex: 3,
      });
    }
    return plotLines;
  }

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

  $(".seo-client-search-form").submit(async function (e) {
    e.preventDefault();
    initGraphs();
  });
})(jQuery);
