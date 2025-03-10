<?php

use Illuminate\Http\UploadedFile;


trait HasAvatar
{
    public function getAvatarAttribute(): string
    {
        return $this->avatar ?? 'default.jpg';
    }

    public function getAvatarUrlAttribute(): string
    {
        return asset('storage/avatars/' . $this->avatar);
    }

    public function saveAvatar(UploadedFile $file): void
    {
        $this->deleteAvatar();
        $file->storeAs('avatars', $this->id . '.' . $file->getClientOriginalExtension());
    }

    public function deleteAvatar()
    {
      
    }
}