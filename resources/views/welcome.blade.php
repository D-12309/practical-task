<!DOCTYPE html>
<html>
<head>
    <title>Laravel Project</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body>
<h2>Laravel Practical Tasks</h2>
<input type="text" id="firstName" placeholder="First Name"> <br>
<input type="text" id="lastName" placeholder="Last Name"> <br>
<input type="email" id="email" placeholder="Email"> <br>
<select id="gender">
    <option value="">Select Gender</option>
    <option value="male">Male</option>
    <option value="female">Female</option>
</select> <br>
<input type="password" id="password" placeholder="Password"> <br>
<input type="password" id="rePassword" placeholder="Re-enter Password"> <br>
<input type="hidden" id="id">
<button id="updateBtn">Update</button>

<button id="createBtn">Create</button>
<h1>Lists</h1>
<input type="text" id="search" placeholder="Search by name">
<button id="searchBtn">Search</button>
<br><br>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Gender</th>
        <th>Created At</th>
        <th>Updated At</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody id="lists"></tbody>
</table>

<div id="loader" class="loader" style="display: none;"></div>

<script>
    $(document).ready(function() {
        fetchLists();
        $('#updateBtn').hide();

        $('#searchBtn').on('click', function() {
            searchList();
        });

        $('#createBtn').on('click', function() {
            createList();
        });
        $('#updateBtn').on('click', function() {
            updateList();
        });

        function fetchLists() {
            $('#lists').empty();
            $('#loader').show();

            $.ajax({
                url: '/fetch-lists',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    $('#loader').hide();

                    if (response.length > 0) {
                        $.each(response, function(index, list) {
                            $('#lists').append(`
                                    <tr>
                                        <td>${list.id}</td>
                                        <td>${list.firstName} ${list.lastName}</td>
                                        <td>${list.email}</td>
                                        <td>${list.gender == 0 ? 'male' :'female'}</td>
                                        <td>${list.created_at}</td>
                                        <td>${list.updated_at}</td>
                                        <td>
                                            <button class="editBtn" data-id="${list.id}">Edit</button>
                                        </td>
                                    </tr>
                                `);
                        });

                        $('.editBtn').on('click', function() {
                            var Id = $(this).data('id');
                            editList(Id);
                        });
                    } else {
                        $('#lists').append(`
                                <tr>
                                    <td>No List found</td>
                                </tr>
                            `);
                    }
                }
            });
        }

        function searchList() {
            var search = $('#search').val();

            if (search !== '') {
                $('#lists').empty();
                $('#loader').show();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '/search-lists',
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        search: search
                    },
                    dataType: 'json',
                    success: function(response) {

                        if (response.length > 0) {
                            $.each(response, function(index, list) {
                                $('#lists').append(`
                                        <tr>
                                            <td>${list.id}</td>
                                            <td>${list.firstName} ${list.lastName}</td>
                                            <td>${list.email}</td>
                                            <td>${list.gender}</td>
                                            <td>${list.created_at}</td>
                                            <td>${list.updated_at}</td>
                                            <td>
                                                <button class="editBtn" data-id="${list.id}">Edit</button>

                                            </td>
                                        </tr>
                                    `);
                            });

                            $('.editBtn').on('click', function() {
                                var Id = $(this).data('id');
                                editList(Id);
                            });
                        } else {
                            $('#lists').append(`
                                    <tr>
                                        <td>No List found</td>
                                    </tr>
                                `);
                        }
                    }
                });
            } else {
                fetchLists();
            }
        }

        function updateList() {
            var Id = $('#id').val();
            var firstName = $('#firstName').val();
            var lastName = $('#lastName').val();
            var email = $('#email').val();
            var gender = $('#gender').val();
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Clear previous error messages
            $('.error').remove();

            // Perform client-side validation
            // ... (existing validation code)

            // Perform server-side validation and update the user
            if ($('.error').length === 0) {
                $.ajax({
                    url: '/update-list/' + Id,
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        first_name: firstName,
                        last_name: lastName,
                        email: email,
                        gender: gender
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#id').val('');
                        $('#firstName').val('');
                        $('#lastName').val('');
                        $('#email').val('');
                        $('#gender').val('');
                        $('#createBtn').show();
                        $('#updateBtn').hide();
                        $('#password').show();
                        $('#rePassword').show();
                        fetchLists();
                    }
                });
            }
        }

        function createList() {
            var firstName = $('#firstName').val();
            var lastName = $('#lastName').val();
            var email = $('#email').val();
            var gender = $('#gender').val();
            var password = $('#password').val();
            var rePassword = $('#rePassword').val();
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            $('.error').remove();
            if (firstName === '') {
                $('#firstName').after('<p class="error">First Name is required</p>');
            }

            if (lastName === '') {
                $('#lastName').after('<p class="error">Last Name is required</p>');
            }

            if (email === '') {
                $('#email').after('<p class="error">Email is required</p>');
            } else if (!isValidEmail(email)) {
                $('#email').after('<p class="error">Invalid email format</p>');
            }

            if (gender === '') {
                $('#gender').after('<p class="error">Gender is required</p>');
            }

            if (password === '') {
                $('#password').after('<p class="error">Password is required</p>');
            } else if (password.length < 6) {
                $('#password').after('<p class="error">Password must be at least 6 characters</p>');
            }

            if (rePassword === '') {
                $('#rePassword').after('<p class="error">Please re-enter your password</p>');
            } else if (rePassword !== password) {
                $('#rePassword').after('<p class="error">Passwords do not match</p>');
            }

            if ($('.error').length === 0) {
                $.ajax({
                    url: '/create-list',
                    type: 'POST',
                    data: {
                        _token: csrfToken,
                        first_name: firstName,
                        last_name: lastName,
                        email: email,
                        gender: gender,
                        password: password
                    },
                    dataType: 'json',
                    success: function(response) {
                        $('#firstName').val('');
                        $('#lastName').val('');
                        $('#email').val('');
                        $('#gender').val('');
                        $('#password').val('');
                        $('#rePassword').val('');

                        fetchLists();
                    }
                });
            }
        }

        function editList(Id) {
            $.ajax({
                url: '/get-list/' + Id,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    $('#firstName').val(response.firstName);
                    $('#lastName').val(response.lastName);
                    $('#email').val(response.email);
                    if(response.gender == 0) {
                        $('#gender').val('male');
                    } else {
                        $('#gender').val('female');

                    }
                    $('#id').val(response.id);
                    $('#createBtn').hide();
                    $('#updateBtn').show();
                    $('#password').hide();
                    $('#rePassword').hide();
                }
            });
        }

        function isValidEmail(email) {
            var regex = /^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/;
            return regex.test(email);
        }
    });
</script>
</body>
</html>
