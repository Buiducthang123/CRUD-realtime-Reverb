<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>


# Hướng dẫn xây dựng chức năng CRUD realtime với Laravel Reverb (version 11) 

Laravel 11 được phát hành vào tháng 3/2024 cùng với đó là công cụ mới Laravel Reverb.


![App Screenshot](https://picperf.io/https://picperf.io/https://laravelnews.s3.amazonaws.com/featured-images/laravel-reverb-featured.png)


## Laravel Reverb
Reverb là một gói nguồn mở riêng biệt, là máy chủ WebSocket của bên thứ nhất dành cho các ứng dụng Laravel. Nó giúp tạo điều kiện giao tiếp theo thời gian thực giữa máy khách và máy chủ.

Laravel Reverb có một số tính năng chính: được viết bằng PHP, nhanh và có khả năng mở rộng

Trong bài viết này chúng ta sẽ tìm hiểu cách xây dựng chức năng CRUD được cập nhật real-time giữa server và client. Điều này sẽ giúp bạn hiểu được cách hoạt động, dễ dàng triển khai WebSocket giữa server backend và frontent.



### Công nghệ sử dụng trong bài viết

**Frontent:** HTML, JS

**Backend:** PHP, LARAVEL, MYSQL

#### Điều kiện tiên quyết:
- PHP sử dụng phiên bản 8.2 trở lên ( sử dụng lệnh để kiểm tra nó )
```bash
  npm run test
```
- Đã cài đặt composer ( hãy kiểm tra nó )
```bash
  composer
```
- Node.js: Phiên bản 20 trở lên ( sử dụng lệnh để kiểm tra nó )
```bash
  node -v
```
- MYSQL : Phiên bản 5.7 trở lên (có thể dùng XAMPP)
```bash
  mysql --version
```

## Triển khai dự án 

#### Cài đặt LARAVEL 11
```bash
  Laravel new project-name
```
Sau khi ứng dụng đã được cài đặt hãy kiểm tra để chắc chắn nó đã hoạt động tốt:
```bash
  php artisan serve
```
Giao diện khi khởi động thành công:

![App Screenshot](https://firebasestorage.googleapis.com/v0/b/thang-3410e.appspot.com/o/Screenshot%202024-04-12%20104154.png?alt=media&token=fa4c4db8-a7b7-41cc-998d-de61dbb8c848)

Hãy kiểm tra file .env của dự án và sửa nó sang như này nếu bạn đang sử dụng mysql
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_name
DB_USERNAME=root
DB_PASSWORD=
```
#### Cài đặt xác thực
Bạn có thể lựa chọn một số gói có sẵn của Laravel như:
- Laravel Brezee
- Laravel Fortify
- Laravel Jetstream

Ở đây tôi sẽ chọn Brezee để phát triển dự án này:
```bash
  composer require laravel/breeze --dev
  php artisan breeze:install
  php artisan migrate
  npm install
  npm run dev
```
Bạn có thể đọc nó ở : [https://laravel.com/docs/11.x/starter-kits#laravel-breeze](https://laravel.com/docs/11.x/starter-kits#laravel-breeze) để hiểu biết rõ hơn về nó.

Sau khi cài đặt xong hãy kiểm tra nó ( Hãy thử kiểm tra các chức năng để chắc chắn rằng nó đã chạy ):

![App Screenshot](https://firebasestorage.googleapis.com/v0/b/thang-3410e.appspot.com/o/Screenshot%202024-04-12%20105635.png?alt=media&token=420284c7-2c89-4322-a1e8-dadef6ebebdc)


### Mô tả rõ hơn về ví dụ của dự án
- Xây dựng chức năng Tạo và Xóa bài viết được cập nhật realtime
#### Tạo model Post và Controller PostController:
##### Tạo model Post:
```bash
php artisan make:model Post -mrcs

```

Giải thích một chút : -mrcs sẽ tạo ra Model, Controller Resource, Seeder tương ứng với Model Post.

Sau đó vào app/Models/Post.php sửa đổi mã của bạn như sau:

```bash
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $table = "posts";
    protected $fillable =  ["user_id","title","content"];
    function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTimeAttribute(): string {
        return date(
            "d M Y, H:i:s",
            strtotime($this->attributes['created_at'])
        );
    }
}

```

Vào file database/migrations/2024_04_12_040016_create_posts_table.php để thêm một số thuộc tính vào bảng :

```bash
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('title');
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

```
Một user có thể tạo nhiều Post nên quan hệ giữa users và posts là quan hệ 1-n. Bảng Post sẽ có các thuộc tính: id, user_id, title,content.

Hãy tối ưu hóa bộ nhớ đệm:
```bash
php artisan optimize
```
Sau đó migrate để thêm bảng post vào cơ sở dữ liệu:

```bash
php artisan migrate
php artisan migrate:refresh

```

Tiếp tục vào file app/Http/Controllers/PostController.php để chỉnh sửa.

```bash
<?php

namespace App\Http\Controllers;

use App\Events\DeletePost;
use App\Jobs\CreatePostJob;
use App\Jobs\DeletePostJob;
use App\Models\Post;
use Illuminate\Http\Request;
use Laravel\Reverb\Loggers\Log;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Hiển thị các bài post
        $posts = Post::all();
        return response()->json($posts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //Trả về giao diện form tạo Post
        return view('post.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Hàm thuực hiện thêm Post
        $post = Post::create(
            [
                "user_id"=>auth()->id(),
                "content"=>$request->content,
                "title"=>$request->title,
            ]
        );

        if ($post) {
            // Nếu thành công, bạn có thể chuyển hướng người dùng đến một trang khác hoặc thực hiện hành động khác
            CreatePostJob::dispatch($post);
            // return redirect()->route('posts.index')->with('success', 'Bài viết đã được tạo thành công!');
        } else {
            // Nếu không thành công, bạn có thể trả về một thông báo lỗi
            return back()->withInput()->with('error', 'Đã xảy ra lỗi khi tạo bài viết. Vui lòng thử lại sau!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
        $result = $post->delete();
        if ($result){
            DeletePostJob::dispatch($result);
        }
    }
}
```
#### Thiết lập định tuyến ( Route )
Vào trong file routes/web.php thêm 1 số route:
```bash
<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

```
#### Tạo một event trong Laravel
Bạn cần tạo một event để lắng nghe một sự kiện cụ thể. Ở đây tôi sẽ tạo 2 event để lắng nghe sự kiện tạo hoặc xóa Post.

```bash
php artisan make:event CreatePost
```
```bash
php artisan make:event DeletePost
```
Các event của Laravel cho phép bạn lắng nghe các sự kiện xảy ra trong ứng dụng của bạn.

Tiếp tục truy cập app/Events/CreatePost.php và sửa đổi nó:
```bash
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreatePost implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public array $post)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('thang-chua-co-ny-create'),
        ];
    }
}
```
Khai báo một PrivateChannel tên là thang-chua-co-ny-create bạn có thể đặt tên khác =)) hoặc bạn cũng có thể để một kênh public
Ngoài ra thì bạn có thể tạo một đối số trong hàm tạo để nhận thông báo ở giao diện người dùng.
Nhớ là phải implements ShouldBroadcast giống như trên =)).

Tương tự với app/Events/DeletePost.php
```bash
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DelelePost implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public array $post)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        echo "delete";
        return [
            new PrivateChannel('delete-post'),
        ];
    }
}

```
#### Tạo Laravel Queue Job
Tương tự tôi cũng tạo ra 2 Job để lắng nghe event tương ứng ở trên
```bash
php artisan make:job CreatePostJob
```
và
```bash
php artisan make:job DeletePostJob
```

Trong file app/Jobs/CreatePostJob.php

Hãy dispatch event CreatePost trong handle

```bash
<?php

namespace App\Jobs;

use App\Events\CreatePost;
use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreatePostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Post $post)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        CreatePost::dispatch([
            "id"=>$this->post->id,
            "user_id"=>$this->post->user_id,
            "title"=>$this->post->title,
            "content"=>$this->post->content,
            "time"=>$this->post->time,
        ]);

    }
}

```

Tương tự với  app/Jobs/DeletePostJob.php

```bash
<?php

namespace App\Jobs;

use App\Events\DeletePost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeletePostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $status)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        DeletePost::dispatch([
            'status'=>$this->status
        ]);
    }
}

```

### Cài đặt Laravel Reverb
Chỉ với 1 lệnh duy nhất

```bash
php artisan install:broadcasting
```

sau đó nó sẽ yêu cầu bạn cài một số gói khác( vui lòng cài đặt hết).
Mở file .env của bạn để kiểm tra

```bash
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=327226
REVERB_APP_KEY=3thcntrmyoeyetx9eexb
REVERB_APP_SECRET=ppqgl2z8vxcb6a63i0s1
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

```
Đã thành công

Vào file routes/channels.php thêm 
Khai báo các kênh phát sóng và cơ chế xác thực ( Hiện tại tôi đang để true )
```bash
<?php

use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('thang-chua-co-ny-create', function ($post) {
    return true;
});
Broadcast::channel('thang-chua-co-ny-delete', function ($post) {
    return true;
});
```

### Giao diện phía Frontend

Sửa đổi file views/layouts/app.blade.php

```bash
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <!-- jquery ajax -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>
        <script>
            var csrf_token =  "<?php echo(csrf_token()) ?>"
        </script>
        <script src="{{ asset('js/index.js') }}"></script>

    </body>
</html>


```

Tạo thư mục post

Trong file views/post/index.blade.php

```bash
@extends('layouts.app');

@section('content')
    <div id="postsContainer">

    </div>
@endsection
```

Trong file views/post/create.blade.php

```bash
@extends('layouts.app');

@section('content')
    <form id="postForm">
        @csrf
        <label for="title">Tiêu đề:</label>
        <input type="text" id="title" name="title" style="width: 500px; padding: 5px;">

        <br>

        <label for="content">Nội dung:</label>
        <textarea id="content" name="content" style="width: 500px; height: 200px; padding: 5px;"></textarea>

        <br>

        <button type="submit" style="padding: 5px 10px; background-color: #000; color: #fff;">Thêm Post</button>
    </form>

@endsection

```


Tạo file js trong public/JS/index.js
```bash
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
    const webSocketChannel = "thang-dz";
    const connectWebSocket = () => {
        window.Echo.private(webSocketChannel).listen(
            "CreatePost",
            async (e) => {
                // Khi nhận được sự kiện CreatePost từ WebSocket, gọi lại hàm getData
                getData(`${rootUrl}/posts`);
            }
        );

        window.Echo.private('delete-post').listen(
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
```
#### Giải thích 1 chút về file js:
- getData là hàm get sử dụng ajax jquery để lấy dữ liệu từ hàm index trong PostController
- connectWebSocket là Hàm kết nối với WebSocket lắng nghe các event phản hồi về tư máy chủ
- $("#postForm").submit(function (e) {...});: Gắn một sự kiện submit vào biểu mẫu có id là #postForm để gửi dữ liệu bài viết lên máy chủ thông qua AJAX khi người dùng gửi form.
- sơ sơ thế thui!!

### Chạy chương trình
Đó là các bước cài đặt sau đây là các lệnh thực hiện chương trình
Tối ưu lại bộ Nhớ
```bash
php artisan optimize
```
Khởi chạy máy chủ ảo
```bash
php artisan serve
```
Khởi chạy máy chủ phía FE 
```bash
npm run dev
```
Khởi chạy Reverb WebSocket
```bash
php artisan reverb:start
```
or
```bash
php artisan reverb:start --debug
```
Khởi chạy queue
```bash
php artisan queue:listen
```
#### Bây giờ hãy truy cập đườn dẫn và kiểm tra
Nhấn f12 vào network chọn ws kiểm tra dưới đây là hình ảnh kết nối thành công

![App Screenshot](https://firebasestorage.googleapis.com/v0/b/thang-3410e.appspot.com/o/Screenshot%202024-04-12%20140107.png?alt=media&token=47afcdc8-b031-4de5-912a-fd45990ed91a)

Bây giờ hay đăng nhập tại các trình duyệt khác nhau (Mỗi trình duyệt một tài khoản)
![App Screenshot](https://firebasestorage.googleapis.com/v0/b/thang-3410e.appspot.com/o/Screenshot%202024-04-12%20141502.png?alt=media&token=f2dcfb4d-832d-4acf-89f3-6d78de7f437a)


### Kết luận
Vậy là bạn đã xây dựng thành công chức năng CRUD realtime đơn giản với Laravel Reverb trong phiên bản mới nhất.
Với điều này bạn đã có thể triển khai WebSocket trong ứng dụng 1 cách đầy đủ mà không cần bổ sung dịch vụ cua Pusher hoặc Socket.io
Chúc các bạn may mắn và khum có ny :>
