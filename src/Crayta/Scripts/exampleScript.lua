--- @class ExampleScript : Script<Character>
local ExampleScript = {}

--- Script properties are defined here
ExampleScript.Properties = {
	{
		name               = "health",
		type               = "number",
		tooltip            = "Current health",
		default            = 100,
		min                = 0,
		max                = 100,
		stepSpeed          = 1,
		slowStepSpeed      = 0.1,
		fastStepSpeed      = 10,
		allowFloatingPoint = true,
		options            = { Slow = 0.25, Normal = 1, Fast = 2, Ludicrous = 100 },
		editor             = "slider",
	},
	{
		name    = "day",
		type    = "string",
		default = "Mon",
		options = { Monday = "Mon", Tuesday = "Tue", Wednesday = "Wed" }
	},
	{
		name                = "property",
		type                = "boolean",
		tooltip             = "Current health",
		default             = false,
		editable            = true,
		editableInBasicMode = true,
		container           = nil,
		visibleIf           = function(properties)
			return properties.allowRespawns == true
		end,
	},
	{
		name    = "grip",
		type    = "gripasset",
		tooltip = "Grip",
	},
}

---This function is called on the server when this entity is created
function ExampleScript:Init()
	local animData = {}
	animData.playbackSpeed = 2.0
	animData.events = {
		IsReloadComplete = function()
			Print("Checking if full")
			return self.bullets == self.properties.maxBullets
		end,
		AmmoAdded        = function()
			self.bullets = self.bullets + 1
			Print("Added Bullets, current ammo = " .. self.bullets)
		end
	}
	self:GetEntity():PlayAction("Reload", animData)
end

return ExampleScript
