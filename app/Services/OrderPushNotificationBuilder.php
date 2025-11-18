<?php

namespace App\Services;


use App\Enums\OrderStatus;
use App\Enums\SwitchBox;
use App\Models\FrontendOrder;
use App\Models\NotificationAlert;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class OrderPushNotificationBuilder
{
    public int $orderId;
    public mixed $status;
    public object $order;

    public function __construct($orderId, $status)
    {
        $this->orderId = $orderId;
        $this->status  = $status;
		//dd($status);
        $this->order   = FrontendOrder::find($orderId);
    }

public function send(): void
{
    //dd('Start', $this->order); // هل في order أصلاً؟

    if (!blank($this->order)) {
        $user = User::find($this->order->user_id);
        //dd('User check', $user);

        if (!blank($user)) {

            if (!blank($user->web_token) || !blank($user->device_token)) {
                $fcmTokenArray = [];
				           // dd('User tokens', $user->web_token, $user->device_token);


                if (!blank($user->web_token)) {
                    $fcmTokenArray[] = $user->web_token;
                }
                if (!blank($user->device_token)) {
                    $fcmTokenArray[] = $user->device_token;
                }

                //dd('Tokens:', $fcmTokenArray);
				$orderss = $this->status;
				//dd($orderss);
                $this->message($fcmTokenArray, $this->status, $this->orderId);
				
            }
        }
    }
}

    private function message($fcmTokenArray, $status, $orderId): void
    {
		//dd($status);
        if ($status == OrderStatus::PENDING) {
			//dd($status);
            $this->pending($fcmTokenArray, $orderId);
        } elseif ($status == OrderStatus::ACCEPT) {
			//dd('asdasdas');
            $this->confirmation($fcmTokenArray, $orderId);
        } elseif ($status == OrderStatus::PROCESSING) {
            $this->processing($fcmTokenArray, $orderId);
        } elseif ($status == OrderStatus::OUT_FOR_DELIVERY) {
            $this->outForDelivery($fcmTokenArray, $orderId);
        } elseif ($status == OrderStatus::DELIVERED) {
            $this->delivered($fcmTokenArray, $orderId);
        } elseif ($status == OrderStatus::CANCELED) {
            $this->canceled($fcmTokenArray, $orderId);
        } elseif ($status == OrderStatus::REJECTED) {
            $this->rejected($fcmTokenArray, $orderId);
        } elseif ($status == OrderStatus::RETURNED) {
            $this->returned($fcmTokenArray, $orderId);
        }
    }

    private function notification($fcmTokenArray, $orderId, $message): void
    {
        try {
            $pushNotification = (object)[
                'title'       => 'Order Notification',
                'description' => $message,
                'order_id'    => $orderId
            ];
            $firebase         = new FirebaseService();
            $firebase->sendNotification($pushNotification, $fcmTokenArray, "Order Notification");
        } catch (Exception $e) {
            Log::info($e->getMessage());
        }
    }

    private function pending($fcmTokenArray, $orderId): void
    {
        $notificationAlert = NotificationAlert::where(['language' => 'order_pending_message'])->first();
        if ($notificationAlert && $notificationAlert->push_notification == SwitchBox::ON) {
            $this->notification($fcmTokenArray, $orderId, $notificationAlert->push_notification_message);
        }
    }

    private function confirmation($fcmTokenArray, $orderId): void
    {
        $notificationAlert = NotificationAlert::where(['language' => 'order_confirmation_message'])->first();
        if ($notificationAlert && $notificationAlert->push_notification == SwitchBox::ON) {
			//dd($notificationAlert->push_notification_message);
            $this->notification($fcmTokenArray, $orderId, $notificationAlert->push_notification_message);
        }
    }

    private function processing($fcmTokenArray, $orderId): void
    {
        $notificationAlert = NotificationAlert::where(['language' => 'order_processing_message'])->first();
        if ($notificationAlert && $notificationAlert->push_notification == SwitchBox::ON) {
            $this->notification($fcmTokenArray, $orderId, $notificationAlert->push_notification_message);
        }
    }

    private function outForDelivery($fcmTokenArray, $orderId): void
    {
        $notificationAlert = NotificationAlert::where(['language' => 'order_out_for_delivery_message'])->first();
        if ($notificationAlert && $notificationAlert->push_notification == SwitchBox::ON) {
            $this->notification($fcmTokenArray, $orderId, $notificationAlert->push_notification_message);
        }
    }

    private function delivered($fcmTokenArray, $orderId): void
    {
        $notificationAlert = NotificationAlert::where(['language' => 'order_delivered_message'])->first();
        if ($notificationAlert && $notificationAlert->push_notification == SwitchBox::ON) {
            $this->notification($fcmTokenArray, $orderId, $notificationAlert->push_notification_message);
        }
    }

    private function canceled($fcmTokenArray, $orderId): void
    {
        $notificationAlert = NotificationAlert::where(['language' => 'order_canceled_message'])->first();
        if ($notificationAlert && $notificationAlert->push_notification == SwitchBox::ON) {
            $this->notification($fcmTokenArray, $orderId, $notificationAlert->push_notification_message);
        }
    }

    private function rejected($fcmTokenArray, $orderId): void
    {
        $notificationAlert = NotificationAlert::where(['language' => 'order_rejected_message'])->first();
        if ($notificationAlert && $notificationAlert->push_notification == SwitchBox::ON) {
            $this->notification($fcmTokenArray, $orderId, $notificationAlert->push_notification_message);
        }
    }

    private function returned($fcmTokenArray, $orderId): void
    {
        $notificationAlert = NotificationAlert::where(['language' => 'order_returned_message'])->first();
        if ($notificationAlert && $notificationAlert->push_notification == SwitchBox::ON) {
            $this->notification($fcmTokenArray, $orderId, $notificationAlert->push_notification_message);
        }
    }
}
