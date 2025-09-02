<?php

use App\Http\Controllers\Access\ForgoutController;
use App\Http\Controllers\Access\LoginController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\Content\BoardController;
use App\Http\Controllers\Content\ContentController;
use App\Http\Controllers\Content\QuestionCommentController;
use App\Http\Controllers\Content\QuestionController;
use App\Http\Controllers\Content\TopicController;
use App\Http\Controllers\Notebook\AnswerController;
use App\Http\Controllers\Notebook\NotebookController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Ticket\TicketController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'login'])->name('login');
Route::post('/logon', [LoginController::class, 'logon'])->name('logon');

Route::get('/forgout/{code?}', [ForgoutController::class, 'forgout'])->name('forgout');
Route::post('/forgout-password', [ForgoutController::class, 'forgoutPassword'])->name('forgout-password');
Route::post('/recover-password/{code}', [ForgoutController::class, 'recoverPassword'])->name('recover-password');

Route::middleware(['auth'])->group(function () {

    Route::get('/app', [AppController::class, 'index'])->name('app');

    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets');
    Route::post('/created-ticket', [TicketController::class, 'store'])->name('created-ticket');
    Route::post('/updated-ticket/{id}', [TicketController::class, 'update'])->name('updated-ticket');
    Route::post('/deleted-ticket/{id}', [TicketController::class, 'destroy'])->name('deleted-ticket');

    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/product/{uuid}', [ProductController::class, 'show'])->name('product');
    Route::get('/create-product', [ProductController::class, 'createForm'])->name('create-product');
    Route::post('/created-product', [ProductController::class, 'store'])->name('created-product');
    Route::post('/updated-product/{uuid}', [ProductController::class, 'update'])->name('updated-product');
    Route::post('/deleted-product/{uuid}', [ProductController::class, 'destroy'])->name('deleted-product');

    Route::post('/created-product-item-file/{uuid}', [ProductController::class, 'storeFile'])->name('created-product-item-file');
    Route::post('/deleted-product-item-file/{uuid}/{id}', [ProductController::class, 'destroyFile'])->name('deleted-product-item-file');
    Route::post('/created-product-item-post/{uuid}', [ProductController::class, 'storePost'])->name('created-product-item-post');
    Route::post('/deleted-product-item-post/{uuid}/{id}', [ProductController::class, 'destroyPost'])->name('deleted-product-item-post');

    Route::get('/contents', [ContentController::class, 'index'])->name('contents');
    Route::get('/content/{id}', [ContentController::class, 'show'])->name('content');
    Route::post('/created-content', [ContentController::class, 'store'])->name('created-content');
    Route::post('/updated-content/{id}', [ContentController::class, 'update'])->name('updated-content');
    Route::post('/deleted-content/{id}', [ContentController::class, 'destroy'])->name('deleted-content');

    Route::post('/created-topic', [TopicController::class, 'store'])->name('created-topic');
    Route::post('/updated-topic/{id}', [TopicController::class, 'update'])->name('updated-topic');
    Route::post('/deleted-topic/{id}', [TopicController::class, 'destroy'])->name('deleted-topic');

    Route::get('/boards', [BoardController::class, 'index'])->name('boards');
    Route::post('/created-board', [BoardController::class, 'store'])->name('created-board');
    Route::post('/updated-board/{id}', [BoardController::class, 'update'])->name('updated-board');
    Route::post('/deleted-board/{id}', [BoardController::class, 'destroy'])->name('deleted-board');

    Route::get('/questions/{topic}', [QuestionController::class, 'index'])->name('questions');
    Route::get('/question/{id}', [QuestionController::class, 'show'])->name('question');
    Route::get('/favorited-question/{id}', [QuestionController::class, 'favorited'])->name('favorited-question');
    Route::get('/create-question/{topic}', [QuestionController::class, 'createForm'])->name('create-question');
    Route::post('/created-question/{topic}', [QuestionController::class, 'store'])->name('created-question');
    Route::post('/updated-question/{id}', [QuestionController::class, 'update'])->name('updated-question');
    Route::post('/deleted-question/{id}', [QuestionController::class, 'destroy'])->name('deleted-question');
    Route::post('/created-comment', [QuestionCommentController::class, 'store'])->name('created-comment');
    Route::post('/updated-comment/{id}', [QuestionCommentController::class, 'update'])->name('updated-comment');

    Route::get('/notebooks', [NotebookController::class, 'index'])->name('notebooks');
    Route::get('/notebook/{id}', [NotebookController::class, 'show'])->name('notebook');
    Route::get('/create-notebook', [NotebookController::class, 'create'])->name('create-notebook');
    Route::post('/created-notebook', [NotebookController::class, 'store'])->name('created-notebook');
    Route::post('/updated-notebook/{id}', [NotebookController::class, 'update'])->name('updated-notebook');
    Route::post('/deleted-notebook/{id}', [NotebookController::class, 'destroy'])->name('deleted-notebook');

    Route::get('/users/{role}', [UserController::class, 'index'])->name('users');
    Route::get('/user/{uuid}', [UserController::class, 'show'])->name('user');
    Route::post('/created-user/{role}', [UserController::class, 'store'])->name('created-user');
    Route::post('/updated-user/{uuid}', [UserController::class, 'update'])->name('updated-user');
    Route::post('/deleted-user/{uuid}', [UserController::class, 'destroy'])->name('deleted-user');

    Route::get('/answer/{notebook}/{question?}', [AnswerController::class, 'index'])->name('answer');
    Route::post('/answer-question', [AnswerController::class, 'update'])->name('answer-question');
    Route::post('/delete-question/{id}', [AnswerController::class, 'destroy'])->name('delete-question');

    
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});
