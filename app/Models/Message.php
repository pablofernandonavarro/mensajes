<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['user_id', 'content', 'file_path', 'file_name', 'file_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hasFile(): bool
    {
        return isset($this->file_path) && !empty($this->file_path);
    }

    public function getFileUrl(): ?string
    {
        return isset($this->file_path) && $this->file_path ? asset('storage/' . $this->file_path) : null;
    }
}
