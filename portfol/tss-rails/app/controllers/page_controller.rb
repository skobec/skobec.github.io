class PageController < ApplicationController
  def send_feedback
    UserMailer.feedback(@name,@contact,@message).deliver_now
    redirect_to page_contact_path
  end
end
