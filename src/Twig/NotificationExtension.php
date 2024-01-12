<?php

declare(strict_types=1);

namespace App\Twig;

use App\Repository\NoteRepository;
use App\Repository\PrivateMessageReceivedRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

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

    public function getPrivateMessageNotifications(): string
    {
        if (!$this->enablePrivateMessage) {
            return '';
        }

        $total = $this->privateMessageReceivedRepository->countNotRead();
        if ($total === 0) {
            return '';
        }

        return '<span class="'. self::BADGE_CLASSLIST .'" data-bs-toggle="tooltip" title="messages non lus">'.
            $total . '<span class="visually-hidden">messages non lus</span>
        </span>';
    }
}
