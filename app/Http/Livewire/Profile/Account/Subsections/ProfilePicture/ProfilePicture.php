<?php

namespace App\Http\Livewire\Profile\Account\Subsections\ProfilePicture;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Masmerise\Toaster\Toaster;
use App\Models\ProfilePicture as ProfilePictureModel;
use App\Models\Attachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProfilePicture extends Component
{
    use WithFileUploads;

    public $user;
    
    #[Validate('nullable|image|max:10240')]
    public $profile_picture;
    
    public $currentPicture;
    public $lastUploadTime;
    public $canUpload = true;
    public $remainingTime = null;

    public function mount($user)
    {
        $this->user = $user;
        $this->loadCurrentPicture();
        $this->checkUploadCooldown();
    }

    protected function checkUploadCooldown()
    {
        if ($this->currentPicture) {
            $lastUpload = $this->currentPicture->created_at;
            $cooldownMinutes = 10;
            $nextAllowedUpload = $lastUpload->addMinutes($cooldownMinutes);
            
            if (now()->lt($nextAllowedUpload)) {
                $this->canUpload = false;
                $this->remainingTime = now()->diffInSeconds($nextAllowedUpload);
            } else {
                $this->canUpload = true;
                $this->remainingTime = null;
            }
        }
    }

    protected function loadCurrentPicture()
    {
        $this->currentPicture = ProfilePictureModel::where('user_id', $this->user->id)
            ->where('is_current', true)
            ->with('attachment')
            ->first();
    }

    public function updatedProfilePicture()
    {
        $this->validate();
        
        // Check cooldown before uploading
        $this->checkUploadCooldown();
        if (!$this->canUpload) {
            $minutes = ceil($this->remainingTime / 60);
            Toaster::error("Please wait {$minutes} minute(s) before uploading another profile picture.");
            $this->profile_picture = null;
            return;
        }
        
        try {
            $userId = $this->user->id;
            
            DB::transaction(function () use ($userId) {
                // Mark all existing profile pictures as not current (0)
                ProfilePictureModel::where('user_id', $userId)
                    ->update(['is_current' => 0]);
                
                // Store the file
                $path = $this->profile_picture->store('profile-pictures', 'public');
                
                // Create attachment record
                $attachment = Attachment::create([
                    'attachable_type' => 'App\\Models\\User',
                    'attachable_id' => $userId,
                    'disk' => 'public',
                    'path' => $path,
                    'original_filename' => $this->profile_picture->getClientOriginalName(),
                    'mime_type' => $this->profile_picture->getMimeType(),
                    'size' => $this->profile_picture->getSize(),
                    'uploaded_by_id' => Auth::id(),
                ]);
                
                // Create profile picture record with is_current = 1
                $profilePicture = ProfilePictureModel::create([
                    'user_id' => $userId,
                    'attachment_id' => $attachment->id,
                    'is_current' => 1,
                    'uploaded_by_id' => Auth::id(),
                ]);
                
                // Verify it was saved
                \Log::info('Profile picture created', [
                    'id' => $profilePicture->id,
                    'user_id' => $profilePicture->user_id,
                    'attachment_id' => $profilePicture->attachment_id,
                    'is_current' => $profilePicture->is_current,
                ]);
            });
            
            $this->profile_picture = null;
            $this->loadCurrentPicture();
            $this->checkUploadCooldown();
            
            Toaster::success('Profile picture updated successfully');
            $this->dispatch('refreshSidebarAvatar')->to('profile.show');
        } catch (\Exception $e) {
            Toaster::error('Failed to upload profile picture: ' . $e->getMessage());
        }
    }

    public function removeProfilePicture()
    {
        try {
            DB::transaction(function () {
                if ($this->currentPicture) {
                    // Delete file from storage
                    if (Storage::disk('public')->exists($this->currentPicture->attachment->path)) {
                        Storage::disk('public')->delete($this->currentPicture->attachment->path);
                    }
                    
                    // Mark as not current (0) before soft deleting
                    $this->currentPicture->update(['is_current' => 0]);
                    
                    // Soft delete the profile picture and attachment
                    $this->currentPicture->attachment->delete();
                    $this->currentPicture->delete();
                }
            });
            
            $this->loadCurrentPicture();
            
            Toaster::success('Profile picture removed successfully');
            $this->dispatch('refreshSidebarAvatar')->to('profile.show');
        } catch (\Exception $e) {
            Toaster::error('Failed to remove profile picture: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.profile.account.subsections.profile_picture.index');
    }
}
