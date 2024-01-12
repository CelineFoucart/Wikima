<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\User;
use Twig\TwigFunction;
use App\Repository\NoteRepository;
use Twig\Extension\AbstractExtension;
use App\Repository\PrivateMessageReceivedRepository;

final class NotificationExtension extends AbstractExtension
{
    private const BADGE_CLASSLIST =  'position-absolute top-0 start-0 translate-badge badge rounded-pill bg-danger';

    public function __construct(
        private PrivateMessageReceivedRepository $privateMessageReceivedRepository,
        private NoteRepository $noteRepository,
        private bool $enablePrivateMessage
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('note_notifications', [$this, 'getNoteNotifications'], ['is_safe' => ['html']]),
            new TwigFunction('pm_notifications', [$this, 'getPrivateMessageNotifications'], ['is_safe' => ['html']]),
        ];
    }

    public function getNoteNotifications(): string
    {
        $total = $this->noteRepository->countNotProcessed();
        if ($total === 0) {
            return '';
        }

        return '<span class="'. self::BADGE_CLASSLIST .'" data-bs-toggle="tooltip" title="notes non traitées">'.
            $total . '<span class="visually-hidden">notes non traitées</span>
        </span>';
    }

    public function getPrivateMessageNotifications(?User $user): string
    {
        if (!$this->enablePrivateMessage || $user === null) {
            return '';
        }

        $total = $this->privateMessageReceivedRepository->countNotRead($user);
        if ($total === 0) {
            return '';
        }

        return '<span class="'. self::BADGE_CLASSLIST .'" data-bs-toggle="tooltip" title="messages non lus">'.
            $total . '<span class="visually-hidden">messages non lus</span>
        </span>';
    }
}
