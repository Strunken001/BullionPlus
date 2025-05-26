<div class="container">
<!-- Navigation Bar -->
<nav class="navbar mb-4">
  <div class="nav-wrapper">
    <div class="d-flex align-items-center">
      {{-- <span class="text-lg font-semibold d-lg-none">Select a Product</span> --}}
      <button class="hamburger" id="hamburger-btn" aria-label="Toggle navigation">☰</button>
    </div>
    <div class="nav-container">
      <a class="nav-link" href="#" data-name="airtime">Airtime</a>
      <a class="nav-link" href="#" data-name="data_bundle">Data Bundle</a>
      <a class="nav-link" href="#" data-name="utility_bill">Utility Bill</a>
      <a class="nav-link" href="#" data-name="giftcard">Giftcards</a>
    </div>
  </div>
</nav>

<div class="filters">
    <select class="select2-auto-tokenize" name="iso">
        <option value="all-countries">All Countries</option>
        @foreach (get_all_countries(global_const()::USER) ?? [] as $key => $code)
            <option value="{{ $code->iso2 }}"
                @if (Auth::check() && $code->name === auth()->user()->address->country) @selected(true) @endif>
                {{ $code->name . ' (+' . remove_speacial_char($code->mobile_code) . ')' }}
            </option>
        @endforeach
    </select>
</div>

<div class="table-responsive">
    <table id="pricing-table">
        <thead>
        <tr>
            <th>SKU</th>
            <th>Country</th>
            <th>Product</th>
            <th>Currency</th>
            <th>API Discounts</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td span="5">Loading...</td>
        </tr>
        </tbody>
    </table>
</div>

<div class="pagination" id="pagination"></div>
</div>

@push('css')

<style>
    .navbar {
        padding: 16px 24px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .nav-container {
        display: flex;
        align-items: center;
        gap: 32px;
        flex-wrap: wrap;
    }

    .nav-link {
        text-decoration: none;
        font-size: 1rem;
        font-weight: 500;
        color: #333;
        padding: 6px 12px;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .nav-link:hover {
        background-color: #e9f5ff;
        color: #0077cc;
    }

    .nav-link.active {
      background-color: #0077cc;
      color: white;
    }

    .container {
      max-width: 1200px;
      margin: 20px auto;
      padding: 0 20px;
    }

    h1 {
      font-size: 1.8rem;
      margin-bottom: 20px;
    }

    .filters {
      display: flex;
      gap: 12px;
      margin-bottom: 20px;
      flex-wrap: wrap;
      width: 200px
    }

    select {
      padding: 8px 12px;
      font-size: 1rem;
      border: 1px solid #ccc;
      border-radius: 6px;
      background-color: #fff;
      color: #333;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
    }

    th, td {
      text-align: left;
      padding: 12px 15px;
      border-bottom: 1px solid #ccc;
    }

    th {
      background-color: #f5faff;
      font-weight: bold;
    }

    .button {
      background-color: #0077cc;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 4px;
      font-size: 0.9rem;
      cursor: pointer;
    }

    .button:hover {
      background-color: #005fa3;
    }

    .pagination {
        margin-top: 20px;
        display: flex;
        gap: 8px;
        justify-content: right;
    }

    .page-btn {
        padding: 6px 12px;
        font-size: 14px;
        border: 1px solid #ccc;
        background-color: white;
        color: #333;
        cursor: pointer;
        border-radius: 4px;
    }

    .page-btn.active {
        background-color: #0077cc;
        color: white;
        border-color: #0077cc;
    }

    .dots {
        padding: 0 8px;
        color: #6b7280;
        display: flex;
        align-items: center;
        font-size: 14px;
    }

    .hamburger {
    display: none;
    font-size: 24px;
    background: none;
    border: none;
    color: #333;
    cursor: pointer;
    }

    .nav-wrapper {
        width: 100%;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    @media (max-width: 768px) {
        .nav-container {
            display: none;
            flex-direction: column;
            gap: 0;
            margin-top: 10px;
            align-items: flex-start;
        }

        .nav-container.show {
            display: flex;
        }

        .hamburger {
            display: block;
            margin-left: auto;
            font-size: 28px;
        }

        .nav-link {
            width: 100%;
            padding: 12px;
            text-align: left;
            border-top: 1px solid #eee;
        }

        table {
            min-width: 600px;
        }
    }

</style>

@endpush

@push('script')

<script>
  const rowsPerPage = 20;
  const table = document.getElementById('pricing-table');
  const tbody = table.querySelector('tbody');
  const rows = Array.from(tbody.querySelectorAll('tr'));
  const pagination = document.getElementById('pagination');

  function displayPage(page) {
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    rows.forEach((row, index) => {
      row.style.display = index >= start && index < end ? '' : 'none';
    });
  }

  function setupPagination() {
    const pageCount = Math.ceil(rows.length / rowsPerPage);
    pagination.innerHTML = '';

    for (let i = 1; i <= pageCount; i++) {
      const btn = document.createElement('button');
      btn.textContent = i;
      btn.classList.add('page-btn');
      btn.addEventListener('click', () => {
        displayPage(i);
        document.querySelectorAll('.page-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
      });
      pagination.appendChild(btn);
    }

    displayPage(1);
    pagination.querySelector('button').classList.add('active');
  }

  setupPagination();

  const tabUrlMap = {
    'all-airtime': "{{ route('user.mobile.topup.get.all.operators') }}",
    'airtime': "{{ route('user.mobile.topup.get.operator') }}",
    'all-data_bundle': "{{ route('user.data.bundle.get.all.operators') }}",
    'data_bundle': "{{ route('user.data.bundle.get.operators') }}",
    'all-utility_bill': "{{ route('user.utility.bill.get.billers') }}",
    'utility_bill': "{{ route('user.utility.bill.get.billers') }}",
    'all-giftcard': "{{ route('user.gift.card.get.all.products') }}",
    'giftcard': "{{ route('user.gift.card.get.all.products') }}",
  }

$(document).ready(function() {
  let currentPage = 1;
  const rowsPerPage = 20;

  $('#hamburger-btn').on('click', function () {
    $('.nav-container').toggleClass('show');
  });

  $('.nav-link').removeClass('active');
  $('.nav-link').first().addClass('active');

  function fetchData(page = 1) {
    currentPage = page;

    const activeTab = $('.nav-link.active').data('name');
    const selectedCountry = $('.select2-auto-tokenize').val();
    const tbody = $('#pricing-table tbody');

    tbody.html('<tr><td colspan="5">Loading...</td></tr>');

    let urlKey = activeTab;
    if (selectedCountry === 'all-countries') {
        urlKey = 'all-' + activeTab;
    }

    const url = tabUrlMap[urlKey] || tabUrlMap[activeTab];

    if (urlKey === `all-${activeTab}`) {
        $.ajax({
        url: url,
        type: 'GET',
        data: {
            page: currentPage,
            size: rowsPerPage,
        },
        success: function(data) {
            updateTable(data.data.content);
            updatePagination(data.data.totalPages || 1);
        },
        error: function(xhr, status, error) {
            console.error('Error loading data:', error);
            tbody.html('<tr><td colspan="5">Failed to load data</td></tr>');
        }
        });
    } else {
        $.ajax({
        url: url,
        type: 'GET',
        data: {
            page: currentPage,
            size: rowsPerPage,
            iso2: selectedCountry,
        },
        success: function(data) {
            updateTable(data.data?.content || data.data);
            updatePagination(data.data.totalPages || 1);
        },
        error: function(xhr, status, error) {
            console.error('Error loading data:', error);
            tbody.html('<tr><td colspan="5">Failed to load data</td></tr>');
        }
        });
    }
    }

  function updateTable(data) {
    const tbody = $('#pricing-table tbody');
    tbody.empty();

    if (!data || data.length === 0) {
      tbody.append('<tr><td colspan="5">No records found</td></tr>');
      return;
    }

    data.forEach(item => {
      const row = `
        <tr>
          <td>${item?.id || item?.productId}</td>
          <td>${item?.country?.name || item?.countryName}</td>
          <td>${item?.name || item?.productName}</td>
          <td>USD</td>
          <td>${(0.7 * (item?.internationalDiscount || item?.internationalDiscountPercentage || item?.discountPercentage || 0)).toFixed(2)}%</td>
        </tr>`;
      tbody.append(row);
    });
  }

    function updatePagination(totalPages) {
        const pagination = $('#pagination');
        pagination.empty();

        const maxVisible = 7; // Show first, last, current ±2, and ellipses
        const ellipsis = $('<span class="dots">...</span>');

        function createPageButton(i) {
            const btn = $('<button></button>').text(i).addClass('page-btn');
            if (i === currentPage) btn.addClass('active');
            btn.on('click', () => fetchData(i));
            return btn;
        }

        if (totalPages <= maxVisible) {
            for (let i = 1; i <= totalPages; i++) {
            pagination.append(createPageButton(i));
            }
        } else {
            // Always show first page
            pagination.append(createPageButton(1));

            if (currentPage > 4) {
            pagination.append(ellipsis.clone());
            }

            let start = Math.max(2, currentPage - 2);
            let end = Math.min(totalPages - 1, currentPage + 2);

            for (let i = start; i <= end; i++) {
            pagination.append(createPageButton(i));
            }

            if (currentPage < totalPages - 3) {
            pagination.append(ellipsis.clone());
            }

            // Always show last page
            pagination.append(createPageButton(totalPages));
        }
    }


    $('.nav-link').on('click', function(e) {
        e.preventDefault();
        $('.nav-link').removeClass('active');
        $(this).addClass('active');
        $('.nav-container').removeClass('show'); // 👈 hide the menu
        fetchData(1);
    });

    $('.select2-auto-tokenize').on('change', function() {
        fetchData(1); 
    });

    fetchData(1);
});

$(document).on('click', function(e) {
  if (!$(e.target).closest('.nav-wrapper').length && $('.nav-container').hasClass('show')) {
    $('.nav-container').removeClass('show');
  }
});

</script>

@endpush