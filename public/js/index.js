$(document).ready(function () {
    const rootUrl = "http://127.0.0.1:8000";

    function deletePost(postId){
        $.ajax({
            url: "/posts/" + postId,
            type: "DELETE",
            data: {
                _token: csrf_token,
                // Các dữ liệu khác bạn muốn gửi trong yêu cầu
            },
            success: function (response) {
                console.log(response);
                // Xử lý phản hồi từ server tại đây
                // Ví dụ: Hiển thị thông báo thành công mà không load lại trang
                alert("Bài viết đã được xóa thành công!");
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            },
        });
    }

    function getData(url) {
        console.log('get');
        $.ajax({
            type: "GET",
            url: url,
            success: function (data) {
                console.log("ok");
                console.log(data);
                let postHTML = '';
                $('#postsContainer').empty();
                data.forEach(post => {
                    postHTML += `
                        <div class="post">
                            <h2>Title: ${post.title}</h2>
                            <p>User ID: ${post.user_id}</p>
                            <p>Content: ${post.content}</p>
                            <p>Created At: ${post.created_at}</p>
                            <p>Updated At: ${post.updated_at}</p>
                            <button type="button" class="delete-item" data-id="${post.id}">Xóa</button>
                        </div>
                    `;
                });
                $('#postsContainer').html(postHTML);
                // Hiển thị dữ liệu lấy được từ server trong giao diện người dùng
            },
            error: function (xhr, status, error) {
                console.log("XHR Status: " + xhr.status);
                console.log("Status: " + status);
                console.log("Error: " + error);
            },
        });
    }

    getData(`${rootUrl}/posts`);
    const webSocketChannel1 = "thang-chua-co-ny-create";
    const webSocketChannel2 = "thang-chua-co-ny-delete";
    const connectWebSocket = () => {
        window.Echo.private(webSocketChannel1).listen(
            "CreatePost",
            async (e) => {
                // Khi nhận được sự kiện CreatePost từ WebSocket, gọi lại hàm getData
                getData(`${rootUrl}/posts`);
            }
        );

        window.Echo.private(webSocketChannel2).listen(
            "DeletePost",
            async (e) => {
                // Khi nhận được sự kiện DeletePost từ WebSocket, gọi hàm handleDeletePost để xử lý việc xóa bài viết
                getData(`${rootUrl}/posts`);
                console.log('hehe')
            }
        );
    };
    connectWebSocket();

    $("#postForm").submit(function (e) {
        e.preventDefault(); // Ngăn chặn mặc định hành vi của biểu mẫu
        var formData = $(this).serialize();

        $.ajax({
            url: "/posts",
            type: "POST",
            data: formData,
            success: function (response) {
                console.log(response);
                // Xử lý phản hồi từ server tại đây

                // Ví dụ: Hiển thị thông báo thành công mà không load lại trang
                alert("Bài viết đã được tạo thành công!");
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            },
        });
    });
    $(document).on('click', '.delete-item',  function () {
        let id = $(this).data('id');
        // console.log(id)
        deletePost(id);
    })
});
