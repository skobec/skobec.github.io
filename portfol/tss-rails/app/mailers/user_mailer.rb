class UserMailer < ActionMailer::Base
	default from: "info@tss.com"

  def feedback(name, contact, message)
		@name = name
		@contact = contact
		@message = message
		mail to: 'PSimakov1409@gmail.com'
	end
end
