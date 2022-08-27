<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
	use HandlesAuthorization;

	/**
	 * Create a new policy instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	public function delete(User $user, Comment $comment): Response
	{
		return $comment->user_id === $user->id ? Response::allow() : ($comment->quote->user_id === $user->id ? Response::allow() :
		Response::deny('You do not have permission to delete this comment.'));
	}
}
